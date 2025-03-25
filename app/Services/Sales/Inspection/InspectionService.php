<?php

namespace App\Services\Sales\Inspection;

use App\Models\Project\Project;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\Sales\Property\PropertyRepository;
use Carbon\Carbon;

/**
 * Class InspectionService
 *
 * @package App\Services\Sales
 */
class InspectionService
{
    public function __construct(
        private DynamicsCrmClient $dynamicsCrmClient,
        private PropertyRepository $propertyRepository,
    ) {
    }

    public function inspectionDate($articleId): array
    {
        $dates = $this->dynamicsCrmClient->inspectionDate($articleId)['data'];

        if ($dates != null) {
            $dates = collect($dates)->filter(function ($date) {
                return Carbon::parse($date['start']) > Carbon::now();
            })->map(function ($date) {
                return Carbon::parse($date['start'])->format('d-m-Y');
            });

            return $dates->toArray();
        }

        return [];
    }

    public function deleteInspection($id): void
    {
        $data = [
            'id' => $id,
            'status_id' => 7
        ];

        $this->dynamicsCrmClient->updateInspection($data);
    }

    public function getInspection($articleId): array
    {
        $addressProperty = $this->propertyRepository->getById($articleId)->getAddress();
        $inspection = $this->dynamicsCrmClient->inspection($articleId);
        $urlMemo = Project::whereJsonContains('crm_ids', $addressProperty->getId())->first()?->url_memo;
        $data = [];

        if ($addressProperty) {
            if ($addressProperty->getOffice() != null) {
                $address['office'] = $addressProperty->getOffice();
            } else {
                $address['office'] = null;
            }

            if ($addressProperty->getLongitude() != null) {
                $address['longitude'] = $addressProperty->getLongitude();
            } else {
                $address['longitude'] = null;
            }

            if ($addressProperty->getLatitude() != null) {
                $address['latitude'] = $addressProperty->getLatitude();
            } else {
                $address['latitude'] = null;
            }

            if ($address['office'] == null && $address['longitude'] == null && $address['latitude'] == null) {
                $address = null;
            }
        } else {
            $address = null;
        }

        if (count($inspection['data']) == 0) {
            $data = [
                'inspection' => null,
                'status' => 'no_inspection',
                'address' => $address,
                'url_memo' => $urlMemo,
            ];
        } else {
            $inspections = collect($inspection['data'])->where('type_id', '=', 1);

            if ($inspections->count() == 0) {
                $data = [
                    'inspection' => null,
                    'status' => 'no_inspection',
                    'address' => $address,
                    'url_memo' => $urlMemo,
                ];
            } else {
                $inspection = $inspections->sortByDesc('created_at')->first();
                $time = Carbon::parse($inspection['take_date'])->addHours(3)->format('H-i');
                $date = Carbon::parse($inspection['take_date'])->format('d-m-Y');

                if ($inspection['status_id'] == 7) {
                    $data = [
                        'inspection' => null,
                        'status' => 'no_inspection',
                        'address' => $address,
                        'url_memo' => $urlMemo,
                    ];
                } elseif ($inspection['status_id'] == 1) {
                    $data = [
                        'inspection' => [
                            'inspection_id' => $inspection['id'],
                            'room_id' => $inspection['room_id'],
                            'date' => Carbon::parse($inspection['take_date'])->format('d-m-Y'),
                            'time' => $time,
                        ],
                        'status' => 'new',
                        'address' => $address,
                        'url_memo' => $urlMemo,
                    ];
                    // phpcs:disable
                } elseif ($inspection['status_id'] == 2 || $inspection['status_id'] == 3 || $inspection['status_id'] == 9) {
                    // phpcs:enable
                    $data = [
                        'inspection' => [
                            'inspection_id' => $inspection['id'],
                            'room_id' => $inspection['room_id'],
                            'date' => $date,
                            'time' => $time,
                        ],
                        'status' => 'no_change',
                        'address' => $address,
                        'url_memo' => $urlMemo,
                    ];
                } elseif ($inspection['status_id'] == 5) {
                    $data = [
                        'inspection' => [
                            'inspection_id' => $inspection['id'],
                            'room_id' => $inspection['room_id'],
                            'date' => $date,
                            'time' => $time,
                        ],
                        'status' => 'not_accepted',
                        'address' => $address,
                        'url_memo' => $urlMemo,
                    ];
                } elseif ($inspection['status_id'] == 4) {
                    $data = [
                        'inspection' => [
                            'inspection_id' => $inspection['id'],
                            'room_id' => $inspection['room_id'],
                            'date' => $date,
                            'time' => $time,
                        ],
                        'status' => 'accepted',
                        'address' => $address,
                        'url_memo' => $urlMemo,
                    ];
                }
            }
        }

        $data['article_id'] = $articleId;

        return $data;
    }

    public function dateTimes(string $articleId, string $date): array
    {
        $day = Carbon::parse($date)->format('d-m-Y');
        $times = $this->dynamicsCrmClient->inspectionDateTimes($articleId, $day);

        if ($times != null) {
            $timeWithoutUtc = collect($times)->where('open', '=', true)->pluck('time')->toArray();
            return array_map(fn($time) => Carbon::parse($time)->addHours(3)->format('H:i'), $timeWithoutUtc);
        }

        return [];
    }

    public function createInspection($request): void
    {
        $data = [
            'type_id' => 1,
            'room_id' => $request->get('room_id'),
            'take_date_date' => Carbon::parse($request->get('date'))->format('d-m-Y'),
            'take_date_time' => Carbon::parse($request->get('time'))->subHours(3)->format('H:i'),
            'status_id' => 1,
        ];

        $this->dynamicsCrmClient->createInspection($data);
    }

    public function updateInspection($request, string $inspectionId): void
    {
        $data = [
            'id' => $inspectionId,
            'take_date_date' => $request->get('date'),
            'take_date_time' => $request->get('time')
        ];

        $this->dynamicsCrmClient->updateInspection($data);
    }
}
