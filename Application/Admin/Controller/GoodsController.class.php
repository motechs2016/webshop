<?php
namespace Admin\Controller;

class GoodsController extends AdminController{
	function index(){
			$goods = $this->lists('goods');
			$this->assign('_list', $goods);
			
			$this->meta_title = '商品信息';
			$this->display();
	}
	
	function add(){
		if(IS_POST){
			$db = M('Goods');
            if($db->create()){
                $id = $db->add();
                if($id){
                	$this->success('添加商品成功',U('index'));
                }else{
                    $this->error($db->getDbError());
                }
            }else{
                $this->error($db->getError());
            }
		}else{
            $goods = array('id'=>0,'name'=>'','price'=>0,'url'=>'');
            $this->assign('goods',$goods);
            $this->display('form');
        }

	}

    function edit(){
        $id = I('id','intval',0);
        $db = M('Goods');
        if(IS_POST){

            if(!$id) $this->error('未获取到id');

            if($db->create()){
                  $res = $db->save();
                  if($res){
                      $this->success('修改成功',U('index'));
                  }else{
                      $this->error($db->getDbError());
                  }
            }else{
                $this->error($db->getError());
            }
        }else{

            $goods = $db->find($id);
            $this->assign('goods',$goods);
            $this->display('form');
        }
    }
}