<?php
/**
 *
 * Nextcloud - LLaMa
 *
 * @copyright Copyright (c) 2023 Pavlo Karbovnyk <pkarbovn@gmail.com>
 *
 * @license AGPL-3.0-or-later
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

return [
    'routes' => [

        ['name' => 'message#getNewestMessages', 'url' => '/msg-last',   'verb' => 'PUT'],
        ['name' => 'message#getRoomMessages',   'url' => '/msg-get',    'verb' => 'PUT'],
        ['name' => 'message#addRoomMessage',    'url' => '/msg-send',   'verb' => 'PUT'],
        ['name' => 'message#updateRoomMessage', 'url' => '/msg-update', 'verb' => 'PUT'],

        ['name' => 'message#updateMessageSeen',     'url' => '/msg-seen',   'verb' => 'PUT'],
        ['name' => 'message#updateMessageReaction', 'url' => '/msg-react',  'verb' => 'PUT'],

        ['name' => 'message#addFileData',    'url' => '/msg-file',        'verb' => 'PUT'],
        ['name' => 'message#updateFileInfo', 'url' => '/msg-file-update', 'verb' => 'PUT'],
        ['name' => 'message#testCompletion', 'url' => '/msg-test-comp',   'verb' => 'PUT'],

        ['name' => 'message#deleteMessage',  'url' => '/msg-del',         'verb' => 'PUT'],
        ['name' => 'message#deleteFileData', 'url' => '/msg-file-del',    'verb' => 'PUT'],
    ],
];
