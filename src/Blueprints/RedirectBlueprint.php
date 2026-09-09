<?php

namespace Pecotamic\Redirect\Blueprints;

use Statamic\Facades\Blueprint;

class RedirectBlueprint extends Blueprint
{
    public static function make(): \Statamic\Fields\Blueprint
    {
        return Blueprint::make()->setContents([
            'fields' => [
                [
                    'handle' => 'request_uri',
                    'field' => [
                        'type' => 'text',
                        'display' => __('redirect::messages.request_uri_display'),
                        'placeholder' => '/...',
                        'antlers' => false,
                        'width' => 66,
                        'listable' => true,
                        'validate' => ['required'],
                    ],
                ],
                [
                    'handle' => 'match_type',
                    'field' => [
                        'type' => 'select',
                        'display' => __('redirect::messages.match_type_display'),
                        'options' => [
                            'exact' => __('redirect::messages.match_type_option_exact'),
                            'starts_with' => __('redirect::messages.match_type_option_starts_with'),
                        ],
                        'default' => 'exact',
                        'clearable' => false,
                        'width' => 33,
                        'listable' => true,
                        'validate' => ['required'],
                    ],
                ],
                [
                    'handle' => 'response_code',
                    'field' => [
                        'type' => 'select',
                        'display' => __('redirect::messages.response_code_display'),
                        'options' => [
                            301 => 'Moved Permanently (301)',
                            302 => 'Moved Temporarily (302)',
                            403 => 'Forbidden (403)',
                            404 => 'Not Found (404)',
                            410 => 'Gone (410)',
                        ],
                        'default' => '301',
                        'clearable' => false,
                        'width' => 33,
                        'listable' => true,
                        'validate' => ['required'],
                    ],
                ],
                [
                    'handle' => 'target',
                    'field' => [
                        'type' => 'text',
                        'display' => __('redirect::messages.target_display'),
                        'antlers' => false,
                        'width' => 66,
                        'listable' => true,
                        'validate' => [
                            'nullable',
                            'required_if:response_code,301,302',
                            'regex:/^(\/\S*|https?:\/\/\S+)$/',
                        ],
                        'if_any' => ['response_code' => 'contains_any 301, 302'],
                    ],
                ],
            ],
        ]);
    }
}
