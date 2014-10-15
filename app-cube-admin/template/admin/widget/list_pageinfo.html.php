<?php $pager = $page_data['pagination']; ?>
<div class='ui_page form-horizontal col-md-1 mt-20 mr-10'>
    <div class='form-group'>
        <select class="col-md-4 __j_pageinfo_num_perpage form-control">
            <?php $pager->o('num_per_page_options', false);?>
        </select>
    </div>
</div>
<ul class="pagination col-md-6"> <?php if ($pager['head_url']): ?> <li><a href="<?php $pager->o('head_url'); ?>">First</a></li>
    <?php else: ?>
    <li class='disabled'><a href="#">First</a></li>
    <?php endif; ?>
    <?php if ($pager['prev_url']): ?>
    <li><a href="<?php $pager->o('prev_url'); ?>">Previous</a></li>
    <?php else: ?>
    <li class='disabled'><a href="#">Previous</a></li>
    <?php endif; ?>
    <?php foreach ($pager['pages'] as $page): ?>
    <?php if ($page['is_current']): ?>
    <li class='active'><a href="<?php $page->o('url'); ?>"><?php $page->o('page'); ?></a></li>
    <?php else: ?>
    <li><a href="<?php $page->o('url'); ?>"><?php $page->o('page'); ?></a></li>
    <?php endif; ?>
    <?php endforeach; ?>
    <?php if ($pager['next_url']): ?>
    <li><a href="<?php $pager->o('next_url'); ?>">Next</a></li>
    <?php else: ?>
    <li class='disabled'><a href="#">Next</a></li>
    <?php endif; ?>
    <?php if ($pager['tail_url']): ?>
    <li><a href="<?php $pager->o('tail_url'); ?>">Last</a></li>
    <?php else: ?>
    <li class='disabled'><a href="#">Last</a></li>
    <?php endif; ?>
</ul>
