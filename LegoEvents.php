<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle;


final class LegoEvents
{
    const onMoveComponents = 'idk.lego.move_components';
    const onResetSortComponents = 'idk.lego.reset_sort_components';
    const onMoveWidgets = 'idk.lego.move_widgets';
    const prePersistAddEntity = 'idk.lego.pre_persist_add_entity';
    const postPersistAddEntity = 'idk.lego.post_persist_add_entity';
    const prePersistEditEntity = 'idk.lego.pre_persist_edit_entity';
    const postPersistEditEntity = 'idk.lego.post_persist_edit_entity';
    const preDeleteEntity = 'idk.lego.pre_delete_entity';
    const postDeleteEntity = 'idk.lego.post_delete_entity';

}
