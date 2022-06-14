<div style="margin: 20px;">
    <?php if($args['errors']): ?>
        <p style="padding: 5px 10px; color: white; background: red;">ERROR: <?=$args['errors'] ?></p>
    <?php endif; ?>
</div>

<div style="margin: 20px;">
    <form action="" method="post">
        <p>To test: TWWDP8-N6DQ2G-WCWJZT-RX9VJG</p>
        <input type="text" name="key" placeholder="License Key">
        <button type="submit" name="activate">Activate</button>
    </form>
</div>
<hr>
<div style="margin: 20px;">
    <?php if(false === $args['keyDomain']): ?>
        <span style="padding: 10px 20px; color: white; background: red;">Plugin not activated</span>
    <?php else: ?>
        <span style="padding: 10px 20px; color: white; background: green;">Plugin activated!</span>
    <?php endif; ?>
</div>
<hr>
<?php if(false !== $args['keyDomain']): ?>
    <div style="margin: 20px;">
        <form action="" method="post">
            <button type="submit" name="deactivate">Deactivate</button>
        </form>
    </div>
<?php endif; ?>
