<?php

namespace App\Models\Sales;

use Spatie\Enum\Enum;

class SaleDocumentType extends Enum
{
    protected static function values(): array
    {
        return [
            'identification' => '1',
            'powerOfAttorney' => '2',
            'snils' => '4',
            'consentLandSurveyingDraft' => '8',
            'consentLandSurveyingSigned' => '10',
            'dsOther' => '16',
            'spouseConsentDraft' => '32',
            'spouseConsentSigned' => '34',
            'confirmationOfLetterOfCredit' => '64',
            'clientAgreement' => '128',
            'dsBti' => '256',
            'dsFinishing' => '512',
            'clientAgreementDraft' => '1024',
            'clientAgreementRegistered' => '2048',
            'dsBtiRegistered' => '4096',
            'dsFinishingRegistered' => '8192',
            'dsOtherRegistered' => '16384',
            'consentPersonalDataDraft' => '32768',
            'consentPersonalDataSigned' => '32770',
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
            'fromClient' => '524504',
            'commercialOffer' => '3',
            'paymentMemo' => '5',
        ];
    }

    protected static function labels()
    {
        return [
            'identification' => 'Удостоверение  личности',
            'powerOfAttorney' => 'Доверенность',
            'snils' => 'СНИЛС',
            'consentLandSurveyingDraft' => 'Согласие на межевание (Проект)',
            'consentLandSurveyingSigned' => 'Согласие на межевание (Подписанный)',
            'dsOther' => 'Другие ДС',
            'spouseConsentDraft' => 'Согласие супруга (Проект)',
            'spouseConsentSigned' => 'Согласие супруга (Подписанный)',
            'confirmationOfLetterOfCredit' => 'Подтверждение аккредитива',
            'clientAgreement' => 'Клиентский договор',
            'dsBti' => 'ДС по БТИ',
            'dsFinishing' => 'ДС на отделку',
            'clientAgreementDraft' => 'Проект договора',
            'clientAgreementRegistered' => 'Зарегистрированный клиентский договор',
            'dsBtiRegistered' => 'Зарегистрированное ДС по БТИ',
            'dsFinishingRegistered' => 'Зарегистрированная версия ДС на отделку',
            'dsOtherRegistered' => 'Зарегистрированные версии других ДС',
            'consentPersonalDataDraft' => 'Согласие на обработку персональных данных (Проект)',
            'consentPersonalDataSigned' => 'Согласие на обработку персональных данных (Подписанный)',
            'applicationUkepDraft' => 'Заявление на выпуск УКЭП (Проект)',
            'applicationUkepSigned' => 'Заявление на выпуск УКЭП (Подписанный)',
            'applicationBackDs' => 'Заявление на возврат ДС',
            'actDraft' => 'Акт приема-передачи (Проект)',
            'actSigned' => 'Акт приема-передачи (Подписанный)',
            'certificateDraft' => 'Сертификат электронного ключа (Проект)',
            'certificateSigned' => 'Сертификат электронного ключа (Подписанный)',
            'identitySig' => 'Тождественность, sig',
            'passportPage1' => 'Паспорт, страница 1',
            'passportPage2' => 'Паспорт, страница 2',
            'powerOfAttorneyToBank' => 'Доверенность на сотрудника Банка',
            'loanAgreement' => 'Кредитный договор',
            'bankApproval' => 'Одобрение Банка',
            'powerOfAttorneyFromBank' => 'Доверенность от Банка',
            'clientIdentification' => 'Идентификация клиента',
            'documentForAccounting' => 'Документ для бухгалтерии',
            'photoFromApplicant' => 'Фото от Заявителя',
            'photoFromExecutor' => 'Фото от Исполнителя',
            'invoicePayment' => 'Счет на оплату',
            'commercialOffer' => 'Коммерческое предложение',
            'paymentMemo' => 'Памятка по оплате',
        ];
    }
}
