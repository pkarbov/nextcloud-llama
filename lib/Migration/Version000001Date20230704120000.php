<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Pavlo Karbovnyk <pkarbovn@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\LLaMaVirtualUser\Migration;

use Closure;
use OCP\Migration\IOutput;
use OCP\DB\ISchemaWrapper;
use Doctrine\DBAL\Types\Types;
use OCP\Migration\SimpleMigrationStep;

class Version000001Date20230704120000 extends SimpleMigrationStep {

    /**
     * @param IOutput $output
     * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
     * @param array $options
     * @return null|ISchemaWrapper
     */
    public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
        /** @var ISchemaWrapper $schema */
        $schema = $schemaClosure();

        if (!$schema->hasTable('lvr_chat_room')) {
            print("Creating Table: lvr_chat_room\n");

            $table = $schema->createTable('lvr_chat_room');
            // Auto increment id
            $table->addColumn('id', Types::BIGINT, [
                'autoincrement' => true,
                'notnull' => true,
            ]);
            $table->addColumn('last_updated', Types::DATETIMETZ_MUTABLE, [
                'notnull' => true,
            ]);
            $table->setPrimaryKey(['id']);

            $table->addIndex(['id'],           'lvr_room_id_index'         );
            $table->addIndex(['last_updated'], 'lvr_room_lastupdated_index');
        }

        if (!$schema->hasTable('lvr_chat_user')) {
            print("Creating Table: lvr_chat_user\n");

            $table = $schema->createTable('lvr_chat_user');
            // Auto increment id
            $table->addColumn('id', Types::BIGINT, [
                'autoincrement' => true,
                'notnull' => true,
            ]);
            $table->addColumn('username', Types::STRING, [
                'notnull' => true,
                'length' => 256,
            ]);
            $table->addColumn('avatar', Types::TEXT, [
                'notnull' => false,
            ]);

            $table->setPrimaryKey(['id']);

            $table->addIndex(['id'],       'lvr_user_id_index'    );
            $table->addIndex(['avatar'],   'lvr_user_avatar_index');

            $table->addUniqueConstraint(['username'], 'lvr_user_name_unique_index');
        }

        if (!$schema->hasTable('lvr_chat_room_user')) {
            print("Creating Table: lvr_chat_room_user\n");

            $table = $schema->createTable('lvr_chat_room_user');
            // Auto increment id
            $table->addColumn('id', Types::BIGINT, [
                'autoincrement' => true,
                'notnull' => true,
            ]);
            $table->addColumn('id_room', Types::BIGINT, [
                'notnull' => true,
            ]);
            $table->addColumn('id_user', Types::BIGINT, [
                'notnull' => true,
            ]);
            $table->setPrimaryKey(['id']);

            $table->addIndex(['id'],      'lvr_room_user_id_index');
            $table->addIndex(['id_user'], 'lvr_user_id_index_fk'  );
            $table->addIndex(['id_room'], 'lvr_room_id_index_fk'  );

            $table->addUniqueConstraint(['id_user', 'id_room'], 'lvr_room_user_unique_index');

            $table->addForeignKeyConstraint('oc_lvr_chat_room', ['id_room'], ['id'], ['ON UPDATE CASCADE'], 'fk_chat_room_room');
            $table->addForeignKeyConstraint('oc_lvr_chat_room', ['id_room'], ['id'], ['ON DELETE CASCADE'], 'fk_chat_room_room');

            $table->addForeignKeyConstraint('oc_lvr_chat_user', ['id_user'], ['id'], ['ON UPDATE CASCADE'], 'fk_chat_user_user');
            $table->addForeignKeyConstraint('oc_lvr_chat_user', ['id_user'], ['id'], ['ON DELETE CASCADE'], 'fk_chat_user_user');
        }

        if (!$schema->hasTable('lvr_chat_room_msg')) {
            print("Creating Table: lvr_chat_room_msg\n");

            $table = $schema->createTable('lvr_chat_room_msg');
            // Auto increment id
            $table->addColumn('id', Types::BIGINT, [
                'autoincrement' => true,
                'notnull' => true,
            ]);
            $table->addColumn('id_parent', Types::BIGINT, [
                'notnull' => false,
            ]);
            $table->addColumn('id_room_user', Types::BIGINT, [
                'notnull' => true,
            ]);
            $table->addColumn('content', Types::TEXT, [
                'notnull' => true,
            ]);
            $table->addColumn('content_bin', Types::BINARY, [
                'notnull' => false,
            ]);
            $table->addColumn('timestamp_start', Types::DATETIMETZ_IMMUTABLE, [
                'notnull' => true,
            ]);
            $table->addColumn('timestamp_end', Types::DATETIMETZ_IMMUTABLE, [
                'notnull' => true,
            ]);
            $table->addColumn('deleted', Types::DATETIMETZ_IMMUTABLE, [
                'notnull' => false,
            ]);
            $table->setPrimaryKey(['id']);

            $table->addIndex(['id'],              'lvr_msg_id_index'       );
            $table->addIndex(['id_parent'],       'lvr_msg_parent_id_index');
            $table->addIndex(['content'],         'lvr_msg_content_index'  );
            $table->addIndex(['timestamp_start'], 'lvr_msg_start_index'    );
            $table->addIndex(['timestamp_end'],   'lvr_msg_end_index'      );

            $table->addIndex(['id_room_user'], 'lvr_room_user_id_index_fk');

            $table->addForeignKeyConstraint('oc_lvr_chat_room_msg',  ['id_parent'],    ['id'], ['ON UPDATE CASCADE'], 'fk_msg_msg' );
            $table->addForeignKeyConstraint('oc_lvr_chat_room_msg',  ['id_parent'],    ['id'], ['ON DELETE CASCADE'], 'fk_msg_msg' );

            $table->addForeignKeyConstraint('oc_lvr_chat_room_user', ['id_room_user'], ['id'], ['ON UPDATE CASCADE'], 'fk_msg_user');
            $table->addForeignKeyConstraint('oc_lvr_chat_room_user', ['id_room_user'], ['id'], ['ON DELETE CASCADE'], 'fk_msg_user');
        }

        if (!$schema->hasTable('lvr_msg_reaction')) {
            print("Creating Table: lvr_msg_reaction\n");

            $table = $schema->createTable('lvr_msg_reaction');
            // Auto increment id
            $table->addColumn('id', Types::BIGINT, [
                'autoincrement' => true,
                'notnull' => true,
            ]);
            $table->addColumn('id_msg', Types::BIGINT, [
                'notnull' => true,
            ]);
            $table->addColumn('id_user', Types::BIGINT, [
                'notnull' => true,
            ]);
            $table->addColumn('seen', Types::DATETIMETZ_IMMUTABLE, [
                'notnull' => true,
            ]);
            $table->addColumn('reaction', Types::TEXT, [
                'notnull' => false,
            ]);

            $table->setPrimaryKey(['id']);

            $table->addIndex(['id'],      'lvr_msg_reac_id_index'     );
            $table->addIndex(['id_user'], 'lvr_msg_reac_user_index_fk');
            $table->addIndex(['id_msg'],  'lvr_msg_reac_msg_index_fk' );

            $table->addForeignKeyConstraint('oc_lvr_chat_room_msg', ['id_msg'],  ['id'], ['ON UPDATE CASCADE'], 'fk_msg_reac_msg' );
            $table->addForeignKeyConstraint('oc_lvr_chat_room_msg', ['id_msg'],  ['id'], ['ON DELETE CASCADE'], 'fk_msg_reac_msg' );

            $table->addForeignKeyConstraint('oc_lvr_chat_user',     ['id_user'], ['id'], ['ON UPDATE CASCADE'], 'fk_msg_reac_user');
            $table->addForeignKeyConstraint('oc_lvr_chat_user',     ['id_user'], ['id'], ['ON DELETE CASCADE'], 'fk_msg_reac_user');
        }

        if (!$schema->hasTable('lvr_msg_files')) {
            print("Creating Table: lvr_msg_files\n");

            $table = $schema->createTable('lvr_msg_files');
            // Auto increment id
            $table->addColumn('id', Types::BIGINT, [
                'autoincrement' => true,
                'notnull' => true,
            ]);
            $table->addColumn('id_msg', Types::BIGINT, [
                'notnull' => true,
            ]);
            $table->addColumn('name', Types::TEXT, [
                'notnull' => true,
            ]);
            $table->addColumn('size', Types::BIGINT, [
                'notnull' => true,
            ]);
            $table->addColumn('type', Types::TEXT, [
                'notnull' => true,
            ]);
            $table->addColumn('extension', Types::TEXT, [
                'notnull' => true,
            ]);
            $table->addColumn('url', Types::TEXT, [
                'notnull' => true,
            ]);
            $table->addColumn('local_url', Types::TEXT, [
                'notnull' => true,
            ]);
            $table->addColumn('audio', Types::BOOLEAN, [
                'notnull' => false,
            ]);
            $table->addColumn('duration', Types::FLOAT, [
                'notnull' => false,
            ]);

            $table->setPrimaryKey(['id']);

            $table->addIndex(['id_msg'],   'lvr_msg_files_msg_index_fk' );
            $table->addIndex(['name'],     'lvr_msg_files_name_index_fk');
            $table->addIndex(['type'],     'lvr_msg_files_type_index_fk');
            $table->addIndex(['extension'],'lvr_msg_files_ext_index_fk' );
            $table->addIndex(['local_url'],'lvr_msg_files_url_index_fk' );

            $table->addForeignKeyConstraint('oc_lvr_chat_room_msg', ['id_msg'],  ['id'], ['ON UPDATE CASCADE'], 'fk_msg_files_msg');
            $table->addForeignKeyConstraint('oc_lvr_chat_room_msg', ['id_msg'],  ['id'], ['ON DELETE CASCADE'], 'fk_msg_files_msg');
        }

        return $schema;
    }
}
