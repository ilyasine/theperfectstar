<?php
/**
 * Admin Notifications content.
 *
 * Variables
 *
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */

defined('ABSPATH') || exit;
?>
<div class="dup-notifications-message" data-message-id="<?php echo esc_attr($tplData['id']); ?>;">
    <h3 class="dup-notifications-title"><?php echo esc_html($tplData['title']); ?></h3>
    <div class="dup-notifications-content">
        <?php echo $tplData['content']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
    </div>
    <?php foreach ($tplData['btns'] as $btn) : ?>
        <a 
            href="<?php echo esc_attr($btn['url']); ?>" 
            class="button small <?php echo esc_attr($btn['class']); ?>" 
            <?php echo $btn['target'] === '_blank' ? 'target="_blank"' : ''; ?>>
            <?php echo esc_html($btn['text']); ?>
        </a>
    <?php endforeach; ?>
</div>
