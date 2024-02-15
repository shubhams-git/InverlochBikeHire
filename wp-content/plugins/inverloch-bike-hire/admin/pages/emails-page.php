<?php

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

include_once plugin_dir_path(__DIR__) . '../includes/models/EmailModel.php';

$emailModel = new EmailModel();

// Fetching emails for display
$provisional_emails = $emailModel->get_all_emails();

// Determine if we're editing an email
$edit_email_id = isset($_GET['edit']) ? intval($_GET['edit']) : null;
$email_to_edit = $edit_email_id ? $emailModel->get_email_by_id($edit_email_id) : null;

?>

<div class="wrap">

    <h1><?php echo $edit_email_id ? 'Edit email' : 'Email List'; ?></h1>
    <?php if(!$edit_email_id): ?>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><b>Email Type</b></th>
                <th><b>Action</b></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($provisional_emails as $email): ?>
            <tr>
                <td><?php echo ucfirst(esc_html($email->email_type)); ?></td>
                <td>
                    <a href="?page=ibh_emails&edit=<?php echo $email->email_id; ?>" class="button button-primary">Edit</a>
                </td>

            </tr>
            <?php endforeach; ?>
            <?php if (empty($provisional_emails)): ?>
                <tr><td colspan="4">No emails found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php elseif($edit_email_id): ?>
    <form method="post" id="email">
        <input type="hidden" name="entity" value="email">
        <input type="hidden" name="action_type" value="edit">
        <?php if ($edit_email_id): ?>
            <input type="hidden" name="email_id" value="<?php echo esc_attr($edit_email_id); ?>">
        <?php endif; ?>
        <div id="messageContainer"></div>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="email_type">Email Type</label></th>
                <td>
                    <select name="email_type" id="email_type">
                        <option value="<?php echo esc_attr($email_to_edit->email_type); ?>"><?php echo ucfirst(esc_attr($email_to_edit->email_type)); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="email_subject">Email Subject</label></th>
                <td><input type="text" id="email_subject" name="email_subject" required class="large-text" value="<?php echo esc_attr($email_to_edit->subject); ?>"></td>
            </tr>
            <tr>
                <th scope="row"><label for="email_content">Email Content</label></th>
                <td>
                    <textarea name="email_content" id="email_content" required cols="50" rows="13" class="large-text"><?php echo esc_textarea($email_to_edit->content); ?></textarea>
                </td>
            </tr>
            <tr>
                <td><?php submit_button('Update Email Content'); ?></td>
            </tr>
        </table>
    </form>
    <?php endif; ?>

</div>