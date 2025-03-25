<?php

namespace App\Http\Resources\Contract;

use App\Models\V2\Contract\Contract;
use Illuminate\Http\Resources\Json\JsonResource;

class HypothecSupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var Contract $this */
        $bankAddress = $this->getBranchAddressHb();
        if (!$this->getIsDigitalTransaction()) {
            $message = 'Подписание кредитной документации будет проходить в офисе банка
Не забудьте следующий набор документов: Паспорт, СНИЛС, ИНН, согласие супруга (-ги) / брачный договор (в зависимости от условий)
Более подробную информацию по набору документов уточняйте у Вашего менеджера Банка';
        } else {
            $message = 'Подписание кредитной документации проходит в электронном формате (приложение Sing.me)';
            $bankAddress = null;
        }

        return [
            'date' => $this->getBankDate()?->format('dd.mm.yyyy h:i'),
            'bank_address' => $bankAddress,
            'bank_manager_email' => $this->getBankManagerEmail(),
            'bank_manager_phone' => $this->getBankManagerMobilePhone(),
            'bank_manager_name' => $this->getBankManagerFullName(),
            'message' => $message,
        ];
    }
}
