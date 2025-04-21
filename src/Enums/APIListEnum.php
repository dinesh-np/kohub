<?php
namespace DP0\Kohub\Enums;

final class APIListEnum
{
    public const GET_ENTRY_FORM = '/entry';
    public const POST_ENTRY_FORM = '/entry';
    public const SUBMIT_VERIFICATION_CODE = '/verification';
    public const GET_PROFILE_FORM = '/profile';
    public const POST_PROFILE_FORM = '/profile';
    public const GET_CREATE_ENTRANT_FORM = '/create-entrant';
    public const POST_CREATE_ENTRANT_FORM = '/create-entrant';
    public const CHECEK_ENTRY_COUNT = '/check-entries';
    public const GET_ENTRY_FORM_SUCCESS = '/entry-success';
    public const RESEND_MOBILE_VERIFICATION = '/resend';
    public const CHECK_ENTRANT = '/check-entrant';
    public const UPDATE_ENTRANT_MARKETING_CHOICE = '/update-marketing';
    public const GET_SIGNED_URL_FOR_RECIPT_UPLOAD = '/receipt_upload_url';

    public static function toArray(): array
    {
        return [
            'GET_ENTRY_FORM' => self::GET_ENTRY_FORM,
            'POST_ENTRY_FORM' => self::POST_ENTRY_FORM,
            'SUBMIT_VERIFICATION_CODE' => self::SUBMIT_VERIFICATION_CODE,
            'GET_PROFILE_FORM' => self::GET_PROFILE_FORM,
            'POST_PROFILE_FORM' => self::POST_PROFILE_FORM,
            'GET_CREATE_ENTRANT_FORM' => self::GET_CREATE_ENTRANT_FORM,
            'POST_CREATE_ENTRANT_FORM' => self::POST_CREATE_ENTRANT_FORM,
            'CHECEK_ENTRY_COUNT' => self::CHECEK_ENTRY_COUNT,
            'GET_ENTRY_FORM_SUCCESS' => self::GET_ENTRY_FORM_SUCCESS,
            'RESEND_MOBILE_VERIFICATION' => self::RESEND_MOBILE_VERIFICATION,
            'CHECK_ENTRANT' => self::CHECK_ENTRANT,
            'UPDATE_ENTRANT_MARKETING_CHOICE' => self::UPDATE_ENTRANT_MARKETING_CHOICE,
            'GET_SIGNED_URL_FOR_RECIPT_UPLOAD' => self::GET_SIGNED_URL_FOR_RECIPT_UPLOAD,
        ];
    }
}
