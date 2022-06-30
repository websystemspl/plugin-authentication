<div id="ws-plugin-authentication-license-form">
    <div class="ws-plugin-authentication-license-form__section">
        <div class="ws-plugin-authentication-license-form__header">
            <img src="<?=$args['assetsUrl'] ?>/img/logo.svg" class="ws-plugin-authentication-license-form__logo">
            <h1 class="ws-plugin-authentication-license-form__title">
                <?=__('Plugin activate form', 'ws-plugin-authentication') ?>
            </h1>
        </div>
        <p><?=__('for', 'ws-plugin-authentication') ?> <strong><?=$args['pluginName'] ?></strong></p>
    </div>    
    <?php if($args['errors']): ?>
        <div class="ws-plugin-authentication-license-form__section">
            <p class="ws-plugin-authentication-license-form__error"><?=$args['errors'] ?></p>
        </div>
    <?php endif; ?>
    <?php if(false === $args['keyDomain']): ?>
    <div class="ws-plugin-authentication-license-form__section">
        <form action="" method="post" style="max-width: 100%;">
            <input type="text" name="key" placeholder="License Key" class="ws-plugin-authentication-license-form__input">
            <button type="submit" name="activate" class="ws-plugin-authentication-license-form__button ws-plugin-authentication-license-form__button--activate"><?=__('Activate', 'ws-plugin-authentication') ?></button>
        </form>
    </div>
    <?php else: ?>
        <div class="ws-plugin-authentication-license-form__section">
            <div class="ws-plugin-authentication-license-form__status ws-plugin-authentication-license-form__status--active">
                <div><?=__('Plugin is activated', 'ws-plugin-authentication') ?></div>
                <div>
                    <form action="" method="post">
                        <button type="submit" name="deactivate" class="ws-plugin-authentication-license-form__button ws-plugin-authentication-license-form__button--deactivate"><?=__('Deactivate', 'ws-plugin-authentication') ?></button>
                    </form>                    
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>