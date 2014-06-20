<?php
/**
 * Copyright (c) Enalean, 2014. All Rights Reserved.
 *
 * This file is a part of Tuleap.
 *
 * Tuleap is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Tuleap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
 */

require_once dirname(__FILE__) .'/../../include/autoload.php';

class RequestDataFactoryTest extends TuleapTestCase {

    public function setUp() {
        parent::setUp();

        $this->date_metadata_title = stub('Docman_Metadata')->getLabel()->returns('date01');
        stub($this->date_metadata_title)->getId()->returns(15);

        $this->date_metadata_title_2 = stub('Docman_Metadata')->getLabel()->returns('date02');
        stub($this->date_metadata_title_2)->getId()->returns(17);

        $this->text_metadata_title = stub('Docman_Metadata')->getLabel()->returns('text01');
        stub($this->text_metadata_title)->getId()->returns(4);

        $this->text_metadata_title_2 = stub('Docman_Metadata')->getLabel()->returns('text02');
        stub($this->text_metadata_title_2)->getId()->returns(3);

        $this->item = stub('Docman_Item')->getGroupId()->returns(200);

        $this->metadata_factory = mock('Docman_MetadataFactory');
        stub($this->metadata_factory)->getMetadataValue($this->item, $this->date_metadata_title)->returns(1403160945);
        stub($this->metadata_factory)->getMetadataValue($this->item, $this->date_metadata_title_2)->returns(1403160949);
        stub($this->metadata_factory)->getMetadataValue($this->item, $this->text_metadata_title)->returns('val01');
        stub($this->metadata_factory)->getMetadataValue($this->item, $this->text_metadata_title_2)->returns('val02');

        $item_date_metadatas = array(
            $this->date_metadata_title,
            $this->date_metadata_title_2
        );

        stub($this->metadata_factory)->getRealMetadataList(
            false,
            array(PLUGIN_DOCMAN_METADATA_TYPE_DATE)
        )->returns($item_date_metadatas);

        $item_text_metadatas = array(
            $this->text_metadata_title,
            $this->text_metadata_title_2
        );

        stub($this->metadata_factory)->getRealMetadataList(
            false,
            array(
                PLUGIN_DOCMAN_METADATA_TYPE_TEXT,
                PLUGIN_DOCMAN_METADATA_TYPE_STRING
            )
        )->returns($item_text_metadatas);

        $this->request_data_factory = new ElasticSearch_1_2_RequestDataFactory(
            $this->metadata_factory
        );
    }

    public function itBuildsTextMetadataValues() {
        $expected_data = array(
            'property_4' => 'val01',
            'property_3' => 'val02'
        );

        $this->assertEqual(
            $expected_data,
            $this->request_data_factory->getCustomTextualMetadataValue(
                $this->item
            )
        );
    }

    public function itBuildsCustomDateForMapping() {
        $expected_data = array(
            '200' => array(
                'properties' => array(
                    'property_15' => array(
                        'type' => 'date'
                    ),
                    'property_17' => array(
                        'type' => 'date'
                    )
                )
            )
        );

        $mapping = array(
            'docman' => array(
                'mappings' => array(
                    '200' => array(
                        'properties' => array(
                            'title' => array(
                                'type' => 'string'
                            )
                        )
                    )
                )
            )
        );

        $this->assertEqual(
            $expected_data,
            $this->request_data_factory->getPUTDateMappingMetadata(
                $this->item,
                $mapping
            )
        );
    }

    public function itBuildsCustomDateDataForItem() {
        $expected_data = array(
            'property_15' => '2014-06-19',
            'property_17' => '2014-06-19',
        );

        $this->assertEqual(
            $expected_data,
            $this->request_data_factory->getPUTCustomDateData(
                $this->item
            )
        );
    }

    public function itBuildsDataForPutRequestCreateMapping() {
        $hardcoded_metadata_title = stub('Docman_Metadata')->getLabel()->returns('title');
        stub($hardcoded_metadata_title)->getType()->returns(PLUGIN_DOCMAN_METADATA_TYPE_STRING);

        $hardcoded_metadata_description = stub('Docman_Metadata')->getLabel()->returns('description');
        stub($hardcoded_metadata_description)->getType()->returns(PLUGIN_DOCMAN_METADATA_TYPE_TEXT);

        $hardcoded_metadata_owner = stub('Docman_Metadata')->getLabel()->returns('owner');
        stub($hardcoded_metadata_owner)->getType()->returns(PLUGIN_DOCMAN_METADATA_TYPE_STRING);

        $hardcoded_metadata_create_date = stub('Docman_Metadata')->getLabel()->returns('create_date');
        stub($hardcoded_metadata_create_date)->getType()->returns(PLUGIN_DOCMAN_METADATA_TYPE_DATE);

        $hardcoded_metadata_update_date = stub('Docman_Metadata')->getLabel()->returns('update_date');
        stub($hardcoded_metadata_update_date)->getType()->returns(PLUGIN_DOCMAN_METADATA_TYPE_DATE);

        $hardcoded_metadata_status = stub('Docman_Metadata')->getLabel()->returns('status');
        stub($hardcoded_metadata_status)->getType()->returns(PLUGIN_DOCMAN_METADATA_TYPE_LIST);

        $hardcoded_metadata_obsolescence_date = stub('Docman_Metadata')->getLabel()->returns('obsolescence_date');
        stub($hardcoded_metadata_obsolescence_date)->getType()->returns(PLUGIN_DOCMAN_METADATA_TYPE_DATE);

        $hardcoded_metadata = array(
            $hardcoded_metadata_title,
            $hardcoded_metadata_description,
            $hardcoded_metadata_owner,
            $hardcoded_metadata_create_date,
            $hardcoded_metadata_update_date,
            $hardcoded_metadata_update_date,
            $hardcoded_metadata_obsolescence_date
        );

        $project_id = 200;

        $expected_data = array(
            '200' => array(
                'properties' => array(
                    'title' => array(
                        'type' => 'string'
                    ),
                    'description' => array(
                        'type' => 'string'
                    ),
                    'owner' => array(
                        'type' => 'string'
                    ),
                    'create_date' => array(
                        'type' => 'date'
                    ),
                    'update_date' => array(
                        'type' => 'date'
                    ),
                    'obsolescence_date' => array(
                        'type' => 'date'
                    ),
                    'file' => array(
                        'type'   => 'attachment',
                        'fields' => array(
                            'title' => array(
                                'store' => 'yes'
                            ),
                            'file' => array(
                                'term_vector' => 'with_positions_offsets',
                                'store'       => 'yes'
                            )
                        )
                    ),
                    'permissions' => array(
                        'type'  => 'string',
                        'index' => 'not_analyzed'
                    )
                )
            )

        );

        $this->assertEqual(
            $expected_data,
            $this->request_data_factory->getPUTMappingData($hardcoded_metadata, $project_id)
        );
    }

}