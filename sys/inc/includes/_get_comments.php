<?php
$id = (int)$entity->getId();
if (empty($id) || $id < 1) $html = true;


$modelName = ucfirst($this->module) . 'CommentsModel';
$CommentsModel = new $modelName;
$CommentsModel->bindModel('Users');


if (empty($html)) {
	/* pages nav */
	$total = $CommentsModel->getTotal(array('cond' => array('entity_id' => $id)));
	$this->_globalize(array('comments_pagination' => ''));
	
	
	$order_way = ($this->Register['Config']->read('comments_order', $this->module)) ? 'DESC' : 'ASC';
	$params = array('order' => 'date ' . $order_way,);
	$comments = $CommentsModel->getCollection(array('entity_id' => $id), $params);
	if ($comments) {
		foreach ($comments as $comment) {
			$markers = array();
			
			
			// COMMENT ADMIN BAR 
			$ip = ($comment->getIp()) ? $comment->getIp() : 'Unknown';
			$moder_panel = '';
			$adm = false;
			if ($this->ACL->turn(array($this->module, 'edit_comments'), false)) {
				$moder_panel .= get_link(get_img('/sys/img/edit_16x16.png'), 
				'/' . $this->module . '/edit_comment_form/' . $comment->getId()) . '&nbsp;';
				$adm = true;
			}
			
			if ($this->ACL->turn(array($this->module, 'delete_comments'), false)) {
				$moder_panel .= get_link(get_img('/sys/img/delete_16x16.png'), 
				'/' . $this->module . '/delete_comment/' . $comment->getId(), array('onClick' => "return confirm('" . __('Are you sure') . "')")) . '&nbsp;';
				$adm = true;
			}
			
			if ($adm) {
				$moder_panel = get_img('/sys/img/ip.png', array('alt' => 'ip', 'title' => h($ip))) . $moder_panel;
			}
			
			
			$img = array(
				'alt' => 'User avatar',
				'title' => h($comment->getName()),
				'class' => 'ava',
			);
			if ($comment->getUser_id() && file_exists(ROOT . '/sys/avatars/' . $comment->getUser_id() . '.jpg')) {
				$markers['avatar'] = get_img('/sys/avatars/' . $comment->getUser_id() . '.jpg', $img);
			} else {
				$markers['avatar'] = get_img('/sys/img/noavatar.png', $img);
			}
			
			
			if ($comment->getUser_id()) {
				$markers['name_a'] = get_link(h($comment->getName()), '/users/info/' . (int)$comment->getUser_id());
				$markers['user_url'] = get_url('/users/info/' . (int)$comment->getUser_id());
				$markers['avatar'] = get_link($markers['avatar'], $markers['user_url']);
			} else {
				$markers['name_a'] = h($comment->getName());
			}

			
			$markers['moder_panel'] = $moder_panel;
			$markers['message'] = $this->Textarier->print_page($comment->getMessage());
			$comment->setAdd_markers($markers);
		}
	}
	$html = $this->render('viewcomment.html', array('commentsr' => $comments));

	
} else {
	$html = '';
}

