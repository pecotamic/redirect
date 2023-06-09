<?php

namespace Pecotamic\Redirects\Blueprints;

use Statamic\Facades\Blueprint;

class RedirectsBlueprint extends Blueprint
{
    public static function make(): \Statamic\Fields\Blueprint
    {
        return Blueprint::make()->setContents([
            'sections' => [
                [
                    'display' => 'Redirections',
                    'fields' => [
                        [
                            'handle' => 'redirects',
                            'field' => [
                                'fields' => [
                                    [
                                        'handle' => 'request_uri',
                                        'field' => [
                                            'input_type' => 'text',
                                            'display' => 'Request-URI',
                                            'listable' => 'hidden',
                                            'placeholder' => '/...',
                                            'antlers' => false,
                                            'width' => 25,
                                            'validate' => [
                                                'required'],
                                            'instructions_position' => 'above',
                                            'visibility' => 'visible',
                                            'hide_display' => false,
                                            'localizable' => false,
                                        ],
                                    ],
                                    [
                                        'handle' => 'match_type',
                                        'field' => [
                                            'options' => [
                                                'exact' => 'Genau',
                                                'starts_with' => 'Beginnt mit',
                                            ],
                                            'taggable' => false,
                                            'push_tags' => false,
                                            'multiple' => false,
                                            'clearable' => false,
                                            'searchable' => true,
                                            'cast_booleans' => false,
                                            'type' => 'select',
                                            'display' => 'Übereinstimmung',
                                            'icon' => 'select',
                                            'localizable' => false,
                                            'width' => 25,
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
                                            'display' => 'Antwort',
                                            'default' => '301',
                                            'type' => 'select',
                                            'icon' => 'select',
                                            'listable' => 'hidden',
                                            'width' => 25,
                                            'instructions_position' => 'above',
                                            'visibility' => 'visible',
                                            'hide_display' => false,
                                            'localizable' => false,
                                            'validate' => ['required']],
                                    ],
                                    [
                                        'handle' => 'target',
                                        'field' => [
                                            'input_type' => 'text',
                                            'antlers' => false,
                                            'type' => 'text',
                                            'display' => 'Ziel',
                                            'icon' => 'text',
                                            'localizable' => false,
                                            'listable' => 'hidden',
                                            'instructions_position' => 'above',
                                            'visibility' => 'visible',
                                            'hide_display' => false,
                                            'validate' => [
                                                'sometimes'],
                                            'width' => 25,
                                            'if_any' => ['response_code' => 'contains_any 301, 302']]],
                                ],
                                'mode' => 'table',
                                'reorderable' => true,
                                'display' => 'Weiterleitungen',
                                'type' => 'grid',
                                'icon' => 'grid',
                                'listable' => 'hidden',
                                'localizable' => true,
                                'add_row' => 'Weiterleitung hinzufügen',
                                'instructions_position' => 'above',
                                'visibility' => 'visible',
                                'always_save' => false,
                                'fullscreen' => true,
                                'hide_display' => false,
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
