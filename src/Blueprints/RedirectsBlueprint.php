<?php

namespace Pecotamic\Redirect\Blueprints;

use Statamic\Facades\Blueprint;

class RedirectsBlueprint extends Blueprint
{
    public static function make(): \Statamic\Fields\Blueprint
    {
        return Blueprint::make()->setContents([
            'sections' => [
                [
                    'display' => __('redirect::messages.section_display'),
                    'fields' => [
                        [
                            'handle' => 'redirects',
                            'field' => [
                                'sets' => [
                                    'redirect' => [
                                        'display' => __('redirect::messages.redirect_set_display'),
                                        'fields' => [
                                            [
                                                'handle' => 'request_uri',
                                                'field' => [
                                                    'input_type' => 'text',
                                                    'display' => __('redirect::messages.request_uri_display'),
                                                    'listable' => 'hidden',
                                                    'placeholder' => '/...',
                                                    'antlers' => false,
                                                    'width' => 66,
                                                    'validate' => ['required'],
                                                    'visibility' => 'visible',
                                                    'hide_display' => false,
                                                    'localizable' => false,
                                                    'replicator_preview' => true,
                                                ],
                                            ],
                                            [
                                                'handle' => 'match_type',
                                                'field' => [
                                                    'options' => [
                                                        'exact' => __('redirect::messages.match_type_option_exact'),
                                                        'starts_with' => __('redirect::messages.match_type_option_starts_with'),
                                                    ],
                                                    'taggable' => false,
                                                    'push_tags' => false,
                                                    'multiple' => false,
                                                    'clearable' => false,
                                                    'searchable' => true,
                                                    'cast_booleans' => false,
                                                    'type' => 'select',
                                                    'display' => __('redirect::messages.match_type_display'),
                                                    'icon' => 'select',
                                                    'localizable' => false,
                                                    'width' => 33,
                                                    'validate' => ['required'],
                                                    'listable' => 'hidden',
                                                    'instructions_position' => 'above',
                                                    'visibility' => 'visible',
                                                    'hide_display' => false,
                                                    'default' => 'exact',
                                                ],
                                            ],
                                            [
                                                'handle' => 'response_code',
                                                'field' => [
                                                    'options' => [
                                                        301 => 'Moved Permanently (301)',
                                                        302 => 'Moved Temporarily (302)',
                                                        403 => 'Forbidden (403)',
                                                        404 => 'Not Found (404)',
                                                        410 => 'Gone (410)',
                                                    ],
                                                    'multiple' => false,
                                                    'clearable' => false,
                                                    'searchable' => false,
                                                    'taggable' => false,
                                                    'push_tags' => false,
                                                    'cast_booleans' => false,
                                                    'display' => __('redirect::messages.response_code_display'),
                                                    'default' => '301',
                                                    'type' => 'select',
                                                    'icon' => 'select',
                                                    'listable' => 'hidden',
                                                    'width' => 33,
                                                    'instructions_position' => 'above',
                                                    'visibility' => 'visible',
                                                    'hide_display' => false,
                                                    'localizable' => false,
                                                    'validate' => ['required'],
                                                ],
                                            ],
                                            [
                                                'handle' => 'target',
                                                'field' => [
                                                    'input_type' => 'text',
                                                    'antlers' => false,
                                                    'type' => 'text',
                                                    'display' => __('redirect::messages.target_display'),
                                                    'icon' => 'text',
                                                    'localizable' => false,
                                                    'listable' => 'hidden',
                                                    'instructions_position' => 'above',
                                                    'visibility' => 'visible',
                                                    'hide_display' => false,
                                                    'validate' => [
                                                        'nullable',
                                                        'required_if:response_code,301,302',
                                                        'regex:/^(\/\S*|https?:\/\/\S+)$/',
                                                    ],
                                                    'width' => 66,
                                                    'if_any' => ['response_code' => 'contains_any 301, 302'],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                                'display' => __('redirect::messages.redirects_field_display'),
                                'type' => 'replicator',
                                'listable' => 'hidden',
                                'visibility' => 'visible',
                                'always_save' => false,
                                'fullscreen' => true,
                                'hide_display' => false,
                                'collapse' => true,
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
