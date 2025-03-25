<?php

namespace App\Models\Document;

use Spatie\Enum\Enum;

/**
 * Class DocumentType
 *
 * @method static self identification()
 * @method static self powerOfAttorney()
 * @method static self commercialOffer()
 * @method static self snils()
 * @method static self paymentMemo()
 * @method static self consentLandSurveyingDraft()
 * @method static self consentLandSurveyingSigned()
 * @method static self dsOther()
 * @method static self spouseConsentDraft()
 * @method static self spouseConsentSigned()
 * @method static self spouseConsentSigned1()
 * @method static self spouseConsentSigned2()
 * @method static self confirmationOfLetterOfCredit()
 * @method static self clientAgreement()
 * @method static self dsBti()
 * @method static self dsFinishing()
 * @method static self clientAgreementDraft()
 * @method static self clientAgreementRegistered()
 * @method static self dsBtiRegistered()
 * @method static self dsFinishingRegistered()
 * @method static self dsOtherRegistered()
 * @method static self consentPersonalDataDraft()
 * @method static self dsFinishingRegistered1()
 * @method static self consentPersonalDataSigned()
 * @method static self applicationUkepDraft()
 * @method static self applicationUkepSigned()
 * @method static self applicationBackDs()
 * @method static self actDraft()
 * @method static self actSigned()
 * @method static self dataSigned1()
 * @method static self dataSigned2()
 * @method static self dataSigned3()
 * @method static self dataSigned4()
 * @method static self dataSigned5()
 * @method static self dataSigned6()
 * @method static self certificateDraft()
 * @method static self certificateSigned()
 * @method static self identitySig()
 * @method static self passportPage1()
 * @method static self passportPage2()
 * @method static self powerOfAttorneyToBank()
 * @method static self loanAgreement()
 * @method static self bankApproval()
 * @method static self powerOfAttorneyFromBank()
 * @method static self clientIdentification()
 * @method static self documentForAccounting()
 * @method static self photoFromApplicant()
 * @method static self photoFromExecutor()
 * @method static self invoicePayment()
 * @method static self invoice()
 * @method static self fromCustomer()
 * @method static self fromClient()
 * @method static self dataSigned()
 * @method static self dataDocSigned()
 * @method static self dataDocSigned2()
 * @method static self dataDocSigned3()
 * @method static self dataDocSigned4()
 * @method static self dataDocSigned5()
 *
 * @package App\Models\Document
 */
class DocumentType extends Enum
{
    protected static function values(): array
    {
        return [
            'identification' => '1',
            'powerOfAttorney' => '2',
            'commercialOffer' => '3',
            'snils' => '4',
            'paymentMemo' => '5',
            'consentLandSurveyingDraft' => '8',
            'consentLandSurveyingSigned' => '10',
            'dsOther' => '16',
            'spouseConsentDraft' => '32',
            'spouseConsentSigned' => '34',
            'spouseConsentSigned1' => '36',
            'spouseConsentSigned2' => '38',
            'confirmationOfLetterOfCredit' => '64',
            'clientAgreement' => '128',
            'dsBti' => '256',
            'dsFinishing' => '512',
            'clientAgreementDraft' => '1024',
            'clientAgreementRegistered' => '2048',
            'dsBtiRegistered' => '4096',
            'dsFinishingRegistered' => '8192',
            'dsFinishingRegistered1' => '10035',
            'dsOtherRegistered' => '16384',
            'consentPersonalDataDraft' => '32768',
            'consentPersonalDataSigned' => '32770',
            'dataSigned' => '40001',
            'dataSigned2' => '40002',
            'dataSigned3' => '40003',
            'dataSigned4' => '40004',
            'dataSigned5' => '40005',
            'dataSigned6' => '40016',
            'dataDocSigned' => '40019',
            'dataDocSigned2' => '40020',
            'dataDocSigned3' => '40021',
            'dataDocSigned4' => '40022',
            'dataDocSigned5' => '40023',
            'applicationUkepDraft' => '65536',
            'applicationUkepSigned' => '65538',
            'applicationBackDs' => '131072',
            'actDraft' => '262144',
            'actSigned' => '524288',
            'certificateDraft' => '524388',
            'certificateSigned' => '524488',
            'identitySig' => '524490',
            'passportPage1' => '524491',
            'passportPage2' => '524492',
            'powerOfAttorneyToBank' => '524493',
            'loanAgreement' => '524494',
            'bankApproval' => '524495',
            'powerOfAttorneyFromBank' => '524496',
            'clientIdentification' => '524497',
            'documentForAccounting' => '524499',
            'photoFromApplicant' => '524500',
            'photoFromExecutor' => '524501',
            'invoicePayment' => '524502',
            'invoice' => '524750',
            'fromCustomer' => '524751',
            'fromClient' => '524504'
        ];
    }
}
