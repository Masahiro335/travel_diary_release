<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Comments Controller
 *
 * @property \App\Model\Table\CommentsTable $Comments
 * @method \App\Model\Entity\Comment[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CommentsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Messages', 'Users'],
        ];
        $comments = $this->paginate($this->Comments);

        $this->set(compact('comments'));
    }

    /**
     * View method
     *
     * @param string|null $id Comment id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $comment = $this->Comments->get($id, [
            'contain' => ['Messages', 'Users'],
        ]);

        $this->set(compact('comment'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Comment id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
		if( !$this->request->is('ajax') ) return $this->redirect( ['action'=>'index'] );

		$this->autoRender = false;

		if(empty($this->Auth) || $this->Auth->status_id !== \App\Model\Table\UserStatusesTable::ID['NORMAL']){
			return $this->getResponse()->withStatus(400)->withType('text/plain')->withStringBody('コメントできる権限がありません。');	
		}

        if( empty($id) ){
            $comment = $this->Comments->newEntity([
				'message_id' => $this->request->getData('message_id'),
				'user_id' => $this->Auth->id,
				'comment' => $this->request->getData('comment'),
				'is_deleted' => false,
			]);
        }else{
            $comment = $this->Comments->find()->where([
					'id' => $id,
                    'message_id' => $this->request->getData('message_id'),
					'user_id' => $this->Auth->id,
					'is_deleted' => false,
                ])
				->first();

			if( empty($comment) ) return $this->getResponse()->withStatus(400)->withType('text/plain')->withStringBody('コメントがありません。');

			$comment = $this->Comments->patchEntity($comment, $this->request->getData('comment'));
		}

		if ($this->Comments->save($comment)){
			$this->Messages = \Cake\ORM\TableRegistry::getTableLocator()->get('Messages');

			$message = $this->Messages->find()
				->contain(['Users'])
				->where(['Messages.id' => $comment->message_id])
				->first()
				;

			$message->comment_count = $message->comment_count + 1;
			if($this->Messages->save($message)){
				$comment = $this->Comments->find()
					->contain(['Users'])
					->where([
						'Comments.id' => $comment->id,
					])
					->first();
				$this->set(compact('comment','message'));
				return $this->render('/element/comment');
			}
		}

		return $this->getResponse()->withStatus(400)->withType('text/plain')->withStringBody('コメントの保存に失敗しました。');
    }

    /**
     * Delete method
     *
     * @param string|null $id Comment id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
		if( !$this->request->is('ajax') ) return $this->redirect( ['action'=>'index'] );

		$this->autoRender = false;

		if(empty($this->Auth) || $this->Auth->status_id !== \App\Model\Table\UserStatusesTable::ID['NORMAL']){
			return $this->getResponse()->withStatus(400)->withType('text/plain')->withStringBody('コメント削除権限がありません。');	
		}

        if( empty($id) == false){
			$comment = $this->Comments->find()->where([
				'id' => $id,
				'user_id' => $this->Auth->id,
				'is_deleted' => false,
			])
			->first();
			$comment->is_deleted = true;
        }else{
			return $this->getResponse()->withStatus(400)->withType('text/plain')->withStringBody('データが取得できませんでした。');
		}

		if ($this->Comments->save($comment)){
			$comment = $this->Comments->find()
				->contain(['Users'])
				->where([
					'Comments.id' => $comment->id,
				])
				->first();
			$this->set(compact('comment'));
			return $this->render('/element/comment');
		}

		return $this->getResponse()->withStatus(400)->withType('text/plain')->withStringBody('コメントの保存に失敗しました。');
	}
}
