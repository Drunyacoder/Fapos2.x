<?php
//turn access
$this->ACL->turn(array($this->module, 'delete_comments'));
$id = (!empty($id)) ? (int)$id : 0;
if ($id < 1) redirect('/' . $this->module);


$commClassName = ucfirst($this->module) . 'CommentsModel';
$commModel = new $commClassName;
$comment = $commModel->getById($id);
if ($comment) {
	$entityID = $comment->getEntity_id();
	$comment->delete();
	
	$entity = $this->Model->getById($entityID);
	$entity->setComments($entity->getComments() - 1);
	$entity->save();
}


if ($this->Log) $this->Log->write('delete comment for ' . $this->module, $this->module . ' id(' . $entityID . ')');
return $this->showInfoMessage(__('Comments is deleted'), '/' . $this->module . '/view/' . $entityID );