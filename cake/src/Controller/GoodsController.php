<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Goods Controller
 *
 * @property \App\Model\Table\GoodsTable $Goods
 * @method \App\Model\Entity\Good[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class GoodsController extends AppController
{

    /**
	 * いいねを追加
	 * @author matsubara
	 * Ajaxに出力
	 * @return  boolean
	 */
	public function add()
	{
		if( $this->request->is('ajax') == false) return $this->redirect('/');
		if( empty($this->request->getQuery('message_id')) ) return $this->getResponse()->withStatus(400)->withType('text/plain')->withStringBody('0');

		if(empty($this->Auth) || $this->Auth->status_id !== \App\Model\Table\UserStatusesTable::ID['NORMAL']){
			return $this->getResponse()->withStatus(400)->withType('text/plain')->withStringBody('0');	
		}
		$this->autoRender = false;

		$message_id = $this->request->getQuery('message_id');

		$good = $this->Goods->find()->where([
				'message_id' => $message_id,
				'user_id' => $this->Auth->id,
				'good' => true,
			])
			->first();	
		//いいねが既にある場合	
		if(empty($good) == false) return $this->getResponse()->withStatus(400)->withType('text/plain')->withStringBody('既にいいねがあります。');

		$good = $this->Goods->find()->where([
				'message_id' => $message_id,
				'user_id' => $this->Auth->id,
			])
			->first();
		if(empty($good)){
			$good = $this->Goods->newEntity([
					'message_id' => $message_id,
					'user_id' => $this->Auth->id,
					'good' => true,
				]);
		}else{
			$good->good = true;
		}

		if ($this->Goods->save($good)){
			$this->Messages = \Cake\ORM\TableRegistry::getTableLocator()->get('Messages');

			$message = $this->Messages->get($good->message_id);
			$message->good_count = $message->good_count + 1;
			if($this->Messages->save($message)){
				return $this->getResponse()->withType('text/plain')->withStringBody('いいねの保存できました。');	
			}
		}

		return $this->getResponse()->withStatus(400)->withType('text/plain')->withStringBody('いいねが保存できませんでした。');

	}

	/**
	 * いいねをキャンセル
	 * @author matsubara
	 * Ajaxに出力
	 * @return  boolean
	 */
    public function delete()
	{
		if( $this->request->is('ajax') == false) return $this->redirect('/');
		if( empty($this->request->getQuery('message_id')) ) return $this->getResponse()->withStatus(400)->withType('text/plain')->withStringBody('0');

		if(empty($this->Auth) || $this->Auth->status_id !== \App\Model\Table\UserStatusesTable::ID['NORMAL']){
			return $this->getResponse()->withStatus(400)->withType('text/plain')->withStringBody('削除できる権限がありません。');	
		}
		
		$this->autoRender = false;

		$message_id = $this->request->getQuery('message_id');

		$good = $this->Goods->find()->where([
				'message_id' => $message_id,
				'user_id' => $this->Auth->id,
				'good' => true,
			])
			->first();	
		//いいねがない場合	
		if(empty($good)) return $this->getResponse()->withType('text/plain')->withStringBody('いいねがありません');

		$good->good = false;

		if($this->Goods->save($good)){
			$this->Messages = \Cake\ORM\TableRegistry::getTableLocator()->get('Messages');

			$message = $this->Messages->get($good->message_id);
			$message->good_count = $message->good_count - 1;
			if($this->Messages->save($message)){
				return $this->getResponse()->withType('text/plain')->withStringBody('いいねの削除できました。');	
			}
		}
		return $this->getResponse()->withStatus(400)->withType('text/plain')->withStringBody('いいねの削除に失敗しました。');

	}
}
