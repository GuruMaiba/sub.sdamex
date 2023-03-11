<?php

// use yii\db\Migration;
//
// /**
//  * Handles the creation of table `group`.
//  */
// class m190314_105306_create_group_table extends Migration
// {
//     /**
//      * {@inheritdoc}
//      */
//     public function safeUp()
//     {
//         $this->createTable('group', [
//             'id' => $this->primaryKey(),
//         ]);
//
//         $this->createTable('group_user', [
//             'group_id' => $this->integer()->notNull(),
//             'user_id' => $this->integer()->notNull(),
//         ]);
//
//         // creates index for column `group_id`
//         $this->createIndex(
//             'idx-group_user-group_id',
//             'group_user',
//             'group_id'
//         );
//
//         // creates index for column `user_id`
//         $this->createIndex(
//             'idx-group_user-user_id',
//             'group_user',
//             'user_id'
//         );
//
//         // add foreign key for table `group`
//         $this->addForeignKey(
//             'fk-group_user-group_id',
//             'group_user',
//             'group_id',
//             'group',
//             'id',
//             'CASCADE'
//         );
//
//         // add foreign key for table `user`
//         $this->addForeignKey(
//             'fk-group_user-user_id',
//             'group_user',
//             'user_id',
//             'user',
//             'id',
//             'CASCADE'
//         );
//     }
//
//     /**
//      * {@inheritdoc}
//      */
//     public function safeDown()
//     {
//         $this->dropForeignKey( 'fk-group_user-user_id', 'group_user' );
//         $this->dropForeignKey( 'fk-group_user-group_id', 'group_user' );
//         $this->dropIndex( 'idx-group_user-user_id', 'group_user' );
//         $this->dropIndex( 'idx-group_user-group_id', 'group_user' );
//         $this->dropTable('group_user');
//         $this->dropTable('group');
//     }
// }
