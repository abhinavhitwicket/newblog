<?php
class PostController extends Controller {

	public $_post;
	public $_posts;

	public function filters() {
		return array(
			'checkPost + view, comments, likes, delete',
			);
	}

	public function filterCheckPost($filterChain) {
		if(!isset($_GET['id'])) {
			$this->renderError('Enter User ID.');
		}else{
			$this->_post = Post::model()->active()->findByPk($_GET['id']);
			if(!$this->_post){
				$this->renderError('This id does not exists');
			}

		}
		$filterChain->run();
	}

	public function actionCreate() {
		if(isset($_POST['Post'])) {
			$post = Post::create($_POST['Post']);
			if(!$post->errors) {
				$this->renderSuccess(array('id'=>$post->id));
			} else {
				$this->renderError($this->getErrorMessageFromModelErrors($post));
			}
		} else {
			$this->renderError('Please send post data!');
		}
	}

	public function actionView($id) {
		$this->renderSuccess(array( 'id'=>$this->_post->id,'title'=>$this->_post->title,'post'=>$this->_post->content));
	}

	public function actionSearch($str){		
		$this->_posts = Post::model()->active()->findAll(array('condition'=>"content LIKE :str",'params'=>array('str'=>"%$str%")));
		if($this->_posts){
			$posts_data = array();
			foreach ($this->_posts as $post) {
				$posts_data += array('id'=>$post->id,'content'=>$post->content);
			}
			$this->renderSuccess(array('posts_data'=>$posts_data));
		} 	
		else {
			$this->renderError('No matches found!');
		} 
	}
	
	public function actionComments($id) {
		
		$comments = $this->_post->comments;
		$no_of_comments = $this->_post->comments_count;		
		$comments_data = array();
		foreach ($comments as $comment) {
			$comments_data[] = array('user_id'=>$comment->user_id,'comment'=>$comment->create_comment);
		}
		$this->renderSuccess(array('comment_info'=>"This post has received $no_of_comments comment(s).<br>",'Comments'=>$comments_data));
		
	}

	public function actionLikes($id) {
		
		
		$likes = $this->_post->likes;
		$no_of_likes = $this->_post->likes_count;
		$likes_data = array();
		foreach ($likes as $like) {
			$likes_data[] = array('user_id'=>$like->user_id);
		}
		$this->renderSuccess(array('like_info'=>"This post has received $no_of_likes like(s).<br>", 'post'=>$likes_data));
		foreach ($likes as $like) {
			$this->renderSuccess(array('user_id'=>$this->$like->user_id));
		}
	}

	public function actionDelete($id) {
		if(!$this->_post)
		{
			$this->renderError('Post ID does not exist.');
		}
		else {       
			$this->_post->deactivate();
			$this->_post->save();
			$this->renderSuccess(array('success'=>"Post deleted."));
		}
	}

	public function actionRestore($id){
		$post = Post::model()->deactivated()->findByPk($id);
		if(!$post) {
			$this->renderError('Post ID does not exist.');
		}
		else {       
			if($post->status!=Post::STATUS_ACTIVE){
				$post->activate();
				$this->renderSuccess(array('message'=>"Post restored."));
			}
			else {
				$this->renderSuccess(array('message'=>"Post already exists."));
			}
		}
	}

}