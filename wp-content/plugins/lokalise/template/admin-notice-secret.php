<?php /** @var Lokalise_Model_SecretNotice $model */ ?>
<div class="notice notice-warning">
    <p>
        <?php printf(
            __("Lokalise secret is not set-up, you will not be able to authorize Wordpress integration without it.<br />" .
            "Got to <i>Settings > Lokalise</i> and generate secret.<br />" .
            "Or add <code>%s</code> to <code>wp-config.php</code>.<br />" .
            "Secret has to be at least 64 characters long.<br />" .
            "Here are some random values you can use for secret:", 'lokalise'),
            'define(\'LOKALISE_SECRET\', \'&lt;secret&gt;\');'
        ); ?><br />
        <?php foreach ($model->getSecrets() as $secret) : ?>
            <code><?php echo $secret; ?></code><br />
        <?php endforeach; ?>
    </p>
</div>
