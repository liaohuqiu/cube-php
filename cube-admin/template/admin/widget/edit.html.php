<form class="col-md-offset-2 form-horizontal" action='<?php $page_data->o('post_url'); ?>' method='post'>
    <input type='hidden' name='edit_action_name' value='submit'>
    <?php foreach ($page_data['identity_info'] as $key => $item_value): ?>
    <input type='hidden' name='<?php echo $key; ?>' value='<?php echo $item_value; ?>'>
    <?php endforeach; ?>
    <?php foreach ($page_data['item_list'] as $item): ?>
    <div class="form-group">
        <label class="col-md-2 control-label" for="<?php $item->o('id'); ?>"><?php $item->o('title'); ?></label>
        <div class="col-md-4">
            <?php if ($item['lock']): ?>
            <input class='form-control' id="<?php $item->o('id'); ?>" type="text" name="<?php $item->o('name'); ?>" value='<?php $item->o('value'); ?>' disabled>
            <?php else: ?>
            <?php if ($item['type'] == "textarea"): ?>
            <textarea class='form-control' id='<?php $item->o('id'); ?>' name='<?php $item->o('name'); ?>' placeholder="<?php $item->o('placeholder'); ?>" style="<?php $item->o('style'); ?>"><?php $item->o('value'); ?></textarea>
            <?php elseif ($item['type'] == "checkbox"): ?>
            <div class='checkbox'>
                <label>
                    <input type='checkbox' id='<?php $item->o('id'); ?>' name='<?php $item->o('name'); ?>' value='1' <?php $item->o('checked'); ?> /> <?php $item->o('desc'); ?>
                </label>
            </div>
            <?php elseif ($item['type'] == "select"): ?>
            <select class='form-control' name='<?php $item->o('name'); ?>'>
                <?php foreach ($item['options'] as $value => $title): ?>
                <?php if ($item['value'] == $value): ?>
                <option value='<?php $value ?>' selected="true"><?php $title ?></option>
                <?php else: ?>
                <option value='<?php $value ?>'><?php $title ?></option>
                <?php endif; ?>
                <?php endforeach; ?>
            </select>
            <?php elseif ($item['type'] == "radio"): ?>
            <div class=''>
                <?php foreach ($item['options'] as $value=>$title): ?>
                <label class='radio-inline'>
                    <?php if ($item['value'] == $value): ?>
                    <input id="<?php $item->o('id'); ?>" type="radio" checked="true" value="<?php $value ?>" name='<?php $item->o('name'); ?>' />
                    <?php else: ?>
                    <input id="<?php $item->o('id'); ?>" type="radio" value="<?php $value ?>" name='<?php $item->o('name'); ?>' />
                    <?php endif; ?><?php $title ?>
                </label>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <input class='form-control' id="<?php $item->o('id'); ?>" type="<?php $item->o('type'); ?>" name="<?php $item->o('name'); ?>" placeholder="<?php $item->o('placeholder'); ?>" value='<?php $item->o('value'); ?>'>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-4">
            <button type="submit" class="btn">Submit</button>
        </div>
    </div>
</form>
