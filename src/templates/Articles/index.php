<!-- File: templates/Articles/index.php -->

<h1>記事一覧</h1>

<table>
  <tr>
    <th>タイトル</th>
    <th>作成日時</th>
    <th>操作</th>
  </tr>

  <!-- <?= var_dump($articles) ?> -->

  <!-- ここで、$articles クエリーオブジェクトを繰り返して、記事の情報を出力します -->

  <?php foreach ($articles as $article): ?>
  <tr>
    <td>
      <!-- 記事のタイトルを出力し、リンクを設定します。ここでactionはviewを指定することで、controllerのviewメソッドを呼び出します。 -->
      <?= $this->Html->link($article->title, ['action' => 'view', $article->slug]) ?>
    </td>
    <td>
      <?= $article->created->format(DATE_RFC850) ?>
    </td>
    <td>
      <?= $this->Html->link('編集', ['action' => 'edit', $article->slug]); ?>
      <?= $this->Form->postlink(
        '削除',
        ['action' => 'delete', $article->slug],
        ['confirm' => 'よろしいですか？']
      ); ?>
    </td>
  </tr>
  <?php endforeach; ?>
</table>
