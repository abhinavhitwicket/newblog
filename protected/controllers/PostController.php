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
			$this->_post = Post::model()->active()->findByPk($_GET['id']); //Check null?
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
		if(!$this->_post){
			$this->renderError('This id does not exists');
		}
		else {
			$this->renderSuccess(array('id'=>$this->_post->content)); //Sending only content and that too in the name of id?
		}
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
		if(!$this->_post){ //Remove this
			$this->renderError('Post ID does not exist.');
		}
		else {
			$comments = $this->_post->comments;
			$no_of_comments = $this->_post->comments_count;		
			$posts_data = array(); //Naming
			foreach ($comments as $comment) {
				$posts_data[] = array('user_id'=>$comment->user_id,'comment'=>$comment->create_comment);
			}
			//What is `post` key there?
			$this->renderSuccess(array('comment_info'=>"This post has received $no_of_comments comment(s).<br>",'post'=>$posts_data));
		}
	}

	public function actionLikes($id) {
		
		if(!$this->_post){
			$this->renderError('Post ID does not exist.');
		}
		else{
			$likes = $this->_post->likes;
			$no_of_likes = $this->_post->likes_count;
			$posts_data = array();
			foreach ($likes as $like) {
				$posts_data[] = array('user_id'=>$like->user_id);
			}
			$this->renderSuccess(array('like_info'=>"This post has received $no_of_likes like(s).<br>", 'post'=>$posts_data));


			
			foreach ($likes as $like) {
				$this->renderSuccess(array('user_id'=>$this->$like->user_id));
				echo "<br>";
			}
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
		$post = Post::model()->findByPk($id); //Add deactivate scope
		if(!$post) {
			$this->renderError('Post ID does not exist.');
		}
		else {       
			if($post->status!=Post::STATUS_ACTIVE){
				$post->activate();
				$post->save(); //Why save here? Repeated..
				$this->renderSuccess(array('message'=>"Post restored."));
			}
			else {
				$this->renderSuccess(array('message'=>"Post already exists."));
			}
		}
	}

}
