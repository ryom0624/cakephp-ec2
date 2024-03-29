<h1>
  Articles tagged with
  <?= $this->Text->toList(h($tags), 'or'); ?>
</h1>

<!-- <?= var_dump($tags)?> -->
<!-- <br> -->
<!-- <?= var_dump($articles)?> -->

<section>
<?php foreach ($articles as $article): ?>
  <article>
    <!-- リンクの作成にHtmlHelperを使用 -->
    <h4><?= $this->Html->link(
      $article->title,
      ['controller' => 'Articles', 'action' => 'view', $article->slug],
    ) ?></h4>
    <span><?= h($article->created) ?></span>
  </article>
<?php endforeach ?>
</section>
