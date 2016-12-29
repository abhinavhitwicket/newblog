<?php
class UserController extends Controller {

	public $_user;

	public function filters() {
		return array(
			'checkUser + login, profile, feed, deactivate' 
			);
	}

	public function filterCheckUser($filterChain) {
		if(!$_GET['id']) {
			$this->renderError('Enter User ID.');
		}
		else {
			$this->_user = User::model()->active()->findByPk($_GET['id']);
			if(!$this->_user)
				$this->renderError("Invalid data");
		}
		$filterChain->run();
	}

	public function actionGrid() {
		$dataProvider = new CActiveDataProvider('User');
		$this->widget('zii.widgets.grid.CGridView',
			array(
				'dataProvider'=>$dataProvider,
				)
			);
	}

	public function actionToughGrid() {
		
		$dataProvider = new CActiveDataProvider('User');
		
		
		$this->widget('zii.widgets.grid.CGridView', array(
			'dataProvider'=>$dataProvider,
			'columns'=>array(
				'id',      
				'name',  
				'email:html', 
				),
			));

	}


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
		if($this->_user) {
			$this->renderSuccess(array('user_id'=>$this->_user->id));
		}
	}

	public function actionProfile($id) {
		if($this->_user) {
			$this->renderSuccess(array('id'=>$this->_user->id, 'name'=>$this->_user->name, 'email'=>$this->_user->email));
		}
		
	}

	public function actionFeed($id) { 
		$posts = $this->_user->posts(array('order'=>'created_at DESC', 'limit'=>5)); 
		if(!$posts){
			$this->renderError('No Recent Posts of yours');
		}
		$posts_data = array();
		foreach ($posts as $post) {
			$posts_data[] = array('id'=>$post->id, 'content'=>$post->content);
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
		if(!$this->_user){
			$this->renderError('Id does not exists');
		} else{
			$this->_user->status = 2;
			$this->_user->save();
			$this->renderSuccess(array('message'=>'User deactivated'));  
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