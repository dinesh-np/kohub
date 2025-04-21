<?php

namespace DP0\Kohub\Enums;

enum APIListEnum: string
{
    case GET_ENTRY_FORM = '/entry';
    case POST_ENTRY_FORM = '/entry';
    case SUBMIT_VERIFICATION_CODE = '/verification';
    case GET_PROFILE_FORM = '/profile';
    case POST_PROFILE_FORM = '/profile';
    case GET_CREATE_ENTRANT_FORM = '/create-entrant';
    case POST_CREATE_ENTRANT_FORM = '/create-entrant';
    case CHECEK_ENTRY_COUNT = '/check-entries';
    case GET_ENTRY_FORM_SUCCESS = '/entry-success';
    case RESEND_MOBILE_VERIFICATION = '/resend';
    case CHECK_ENTRANT = '/check-entrant';
    case UPDATE_ENTRANT_MARKETING_CHOICE = '/update-marketing';
    case GET_SIGNED_URL_FOR_RECIPT_UPLOAD = '/receipt_upload_url';

    public static function toArray(): array
    {
        return [
            self::GET_ENTRY_FORM->key => self::GET_ENTRY_FORM->value,
            self::POST_ENTRY_FORM->key => self::POST_ENTRY_FORM->value,
            self::SUBMIT_VERIFICATION_CODE->key => self::SUBMIT_VERIFICATION_CODE->value,
            self::GET_PROFILE_FORM->key => self::GET_PROFILE_FORM->value,
            self::POST_PROFILE_FORM->key => self::POST_PROFILE_FORM->value,
            self::GET_CREATE_ENTRANT_FORM->key => self::GET_CREATE_ENTRANT_FORM->value,
            self::POST_CREATE_ENTRANT_FORM->key => self::POST_CREATE_ENTRANT_FORM->value,
            self::CHECEK_ENTRY_COUNT->key => self::CHECEK_ENTRY_COUNT->value,
            self::GET_ENTRY_FORM_SUCCESS->key => self::GET_ENTRY_FORM_SUCCESS->value,
            self::RESEND_MOBILE_VERIFICATION->key => self::RESEND_MOBILE_VERIFICATION->value,
            self::CHECK_ENTRANT->key => self::CHECK_ENTRANT->value,
            self::UPDATE_ENTRANT_MARKETING_CHOICE->key => self::UPDATE_ENTRANT_MARKETING_CHOICE->value,
            self::GET_SIGNED_URL_FOR_RECIPT_UPLOAD->key => self::GET_SIGNED_URL_FOR_RECIPT_UPLOAD->value,
        ];
    }
}
