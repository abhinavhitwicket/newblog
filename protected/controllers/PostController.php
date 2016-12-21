<?php
class PostController extends Controller {

	public $_post;

	public function filters() {
		return array(
			'checkUser + view, comments, likes'
		);
	}

	public function filterCheckUser($filterChain) {
		if(!isset($_GET['id'])) {
			//ERROR
		}
		$this->_post = Post::model()->active()->findByPk($_GET['id']);
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

	public function actionSearch($str){
			$posts = Post::model()->findAll(array('condition'=>"content LIKE :str", 'params'=>array('str'=>"%$str%")));
			if($posts){
				$posts_data = array();
				foreach ($posts as $post) {
				$posts_data[] = array('id'=>$post->id, 'content'=>$post->content);
			}
			$this->renderSuccess(array('id'=>$post->id, 'posts_data'=>$posts_data));
		} 	else {
			$this->renderError('No matches found!');
			} 

	}

	public function actionView($id) {
		if(!$this->_post){
			$this->renderError('This id does not exists');
		}
		else {
			$this->renderSuccess(array('id'=>$this->_post->content));
		}
	}

	
	public function actionComments($id) {
		
		if(!$this->_post)
		{
			$this->renderError('Post ID does not exist.');
		}
		else {
			$comments = $this->_post->comments;
			$no_of_comments = $this->_post->comments_count;
		    echo "$no_of_comments different comment(s) about this post have been posted.<br>";
			foreach ($comments as $comment) {
				$this->renderSuccess(array('user_id'=>$this->$comment->user_id,'content'=>$this->$comment->content));
				echo "<br>";
			}
		}
	}


	public function actionLikes($id) {
			
			if(!$this->_post){
					$this->renderError('Post ID does not exist.');
			}
			else{
				$likes = $this->_post->likes;
				$no_of_likes = $this->_post->likes_count;
				echo "This post has received $no_of_likes like(s).<br>";
				
				foreach ($likes as $like) {
					$this->renderSuccess(array('user_id'=>$this->$like->user_id));
					echo "<br>";
				}
			}
			
	}

	public function actionDeactivate($id){
	     $post = Post::model()->findByPk($id);
	     if($post->status==1){
	     	$post->status = 2;
	     	$post->save();
	     	$this->renderSuccess(array('message'=>'Deleted'));
	     }
	     else{
	     	$this->renderError('This id is already deleted.');
	     }
    }

}