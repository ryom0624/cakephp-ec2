<?php
// src/Model/Table/ArticlesTable.php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\Query;
use Cake\Utility\Text;
use Cake\Event\EventInterface;
use Cake\Validation\Validator;

class ArticlesTable extends Table
{
    public function initialize(array $config): void
    {
        $this->addBehavior('Timestamp');
        $this->belongsToMany('Tags', [
          'joinTable' => 'articles_tags',
          'dependent' => true
        ]);
    }

    public function beforeSave(EventInterface $event, $entity, $options)
    {
        if ($entity->tag_string) {
          $entity->tags = $this->_buildTags($entity->tag_string);
        }

        if ($entity->isNew() && !$entity->slug) {
            $sluggedTitle = Text::slug($entity->title);
            // スラグをスキーマで定義された最大長に調整
            $entity->slug = substr($sluggedTitle, 0, 191);
        }
    }

    protected function _buildTags($tagString)
    {
      // タグのトリミング
      $newTags = array_map('trim', explode(',', $tagString));
      // すべてのからのタグを削除
      $newTags = array_filter($newTags);
      // 重複タグの削除
      $newTags = array_unique($newTags);

      $out = [];
      $query = $this->Tags->find()
        ->where(['Tags.title IN' => $newTags]);

      // 新しいタグのリストから既存のタグを削除。
      foreach($query->extract('title') as $existing) {
        $index = array_search($existing, $newTags);
        if($index !== false) {
          unset($newTags[$index]);
        }
        // 既存のタグを追加
        foreach($query as $tag) {
          $out[] = $tag;
        }
        // 新しいタグを追加。
        foreach($newTags as $tag) {
          $out = $this->tags->newEntity(['title' => $tag]);
        }
      }
    }

    public function validationDefault(Validator $validator): Validator
    {
      $validator
        ->notEmptyString('title')
        ->minLength('title', 10)
        ->maxLength('title', 255)

        ->notEmptyString('body')
        ->minLength('body', 10);

      return $validator;
    }

    public function findTagged(Query $query, array $options)
    {
      $columns = [
        'Articles.id', 'Articles.user_id', 'Articles.title',
        'Articles.body', 'Articles.published', 'Articles.created',
        'Articles.slug',
      ];

      $query = $query
        ->select($columns)
        ->distinct($columns);

      if (empty($options['tags'])) {
        // タグが指定されていない場合は、タグのない記事を検索します。
        $query->leftJoinWith('Tags')
          ->where(['Tags.title IS' => null]);
      } else {
        // 提供されたタグが1つ以上ある記事を検索します。
        $query->innerJoinWith('Tags')
          ->where(['Tags.title IN' => $options['tags']]);
      }

      return $query->group(['Articles.id']);
    }


}
