<?php
class CommentController extends Controller {

	public function actionCreate() {
		if(isset($_POST['Comment'])) {
			$comment = Comment::create($_POST['Comment']);
			if(!$comment->errors) {
				$this->renderSuccess(array('id'=>$comment->id));
			} else {
				$this->renderError($this->getErrorMessageFromModelErrors($comment));
			}
		}   else{
			$this->renderError('Please send post data!');
		}
	}  

}