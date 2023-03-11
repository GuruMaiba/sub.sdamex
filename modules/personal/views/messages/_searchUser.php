<? if ($model) : ?>
<? foreach ($model as $key => $user) : ?>
    <div class="item" numb='<?=$user['id']?>'>@<?=$user['username']?></div>
<? endforeach; ?>
<? else : ?>
    <div class="item default">Пользователь не найден</div>
<? endif; ?>