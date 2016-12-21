<?php
class UserController extends Controller {

	public function actionCreate() {
		if(isset($_POST['User'])) {
			$user = User::create($_POST['User']);
			if(!$user->errors) {
				$this->renderSuccess(array('user_id'=>$user->id));
			} else {
				$this->renderError($this->getErrorMessageFromModelErrors($user));
			}
		} else {
			$this->renderError('Please Create a new User!');
		}
	}




	public function actionLogin($id) {
		$user = User::model()->findbyPK($id);
		if(!$user) {
			$this->renderError('ERROR! ID not Found');
		}
		else {
			$this->renderSuccess(array('user_id'=>$user->id));
		}
	}

	public function actionProfile($id) {
		$user = User::model()->findbyPK($id);
		if(!$user) {
			$this->renderError('ERROR! Profile not found');
		}
		else {
			$this->renderSuccess(array('user_id'=>$user->id, 'name'=>$user->name, 'email'=>$user->email));
		}
	}


	public function actionFeed($id) {
		$user = User::model()->findByPK($id);  
		$posts = $user->posts(array('order'=>'created_at DESC', 'limit'=>5)); 
		$posts_data = array();
		foreach ($posts as $post) {
			$posts_data[] = array('id'=>$user->id, 'content'=>$user->posts);
		}
		$this->renderSuccess(array('posts_data'=>$posts_data));
	}

	public function actionSearchProfile($name){
		$users = User::model()->findAllByAttributes(array('name'=>$name));
		$users_profile = array();
		if(!$users) {
			$this->renderError('Name not found');
		}
		else{
			foreach($users as $user){
				$users_profile[] = array('user_id'=>$user->id, 'user_name'=>$user->name, 'email'=>$user->email);
			}
			$this->renderSuccess(array('users_profile'=>$users_profile));
		}
	}

	public function actionDeactivate($id){
		$users = User::model()->findByPk($id);
		if($users->status==1){
			$users->status = 2;
			$users->save();
			$this->renderSuccess(array('message'=>'User deactivated'));  
		}
		else{
			$this->renderError('This User id is already deactivated.');
		}
	}

	public function actionReactivate($id){
		$users = User::model()->findByPk($id);
		if($users->status==2){
			$users->status = 1;
			$users->save();
			$this->renderSuccess(array('message'=>'User reactivated'));  
		}
		else{
			$this->renderError('This User id is already active.');
		}
	}


}