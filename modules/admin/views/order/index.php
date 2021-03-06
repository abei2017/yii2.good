<?php
use yii\grid\GridView;
use yii\grid\ActionColumn;
$this->params['breadcrumbs'] = [
    [
        'label'=>'订单管理',
        'url'=>['/admin/order/index']
    ],
    '订单列表'
];
?>


<?= GridView::widget([
    'dataProvider'=>$dataProvider,
    'columns'=>[
        'id:text:订单编号',
        [
            'label'=>'订单信息',
            'format'=>'raw',
            'value'=>function($model){
                $str = "<ul>";
                $str .= "<li>商户编号：{$model->pay_id}</li>";
                $str .= "<li>生成时间：".date('Y-m-d H:i:s',$model->created_at)."</li>";
                if($model->state == 'pay'){
                    $str .= "<li>付款时间：".date('Y-m-d H:i:s',$model->paid_at)."</li>";
                    $str .= "<li>交易编号：{$model->transaction_id}</li>";
                }

                return $str."</ul>";
            }
        ],
        [
            'label'=>'菜品信息',
            'format'=>'raw',
            'value'=>function($model){
                $str = '<ul>';
                foreach($model->dishes as $val){
                    $str .= "{$val->dish->title} {$val->quantity}x{$val->price} = {$val->money}<br/>";
                }
                return $str."</ul>";
            }
        ],
        'quantity',
        [
            'label' => '订单金额',
            'format' => 'raw',
            'value' => function($model){
                return "实付金额：{$model->money}<br/>优惠金额：{$model->preferential_money}";
            }
        ],
        [
            'label'=>'订单类型',
            'format' => 'html',
            'value'=>function($model){
                if($model->type == 'normal'){
                    return '普通订单';
                }elseif ($model->type == 'wx'){
                    $str = "微信特惠订单";
                    $arr = explode('#',$model->type_str);
                    $str .= "<br/>取餐码：{$arr[0]}";

                    if(isset($arr[1])){
                        $str .= "<br/>取餐时间：".date('Y-m-d H:i:s',$arr[1]);
                    }

                    return $str;
                }
            }
        ],
        [
            'label'=>'订单状态',
            'value'=>function($model){
                return $model->stateTxt();
            }
        ],
        [
            'class' => ActionColumn::className(),
            'header'=>'操作',
            'headerOptions'=>['width'=>'120'],
            'template' => '{ispay}<br/>{close}{preferential}',
            'buttons'=>[
                'ispay'=>function($url, $model, $key){
                    if($model->state == 'unpay'){
                        return "<a href='javascript:;' data-url='{$url}' class='_is_pay'>付款核查</a>";
                    }
                    return false;
                },
                'close'=>function($url, $model, $key){
                    if(in_array($model->state,['unpay','fail'])){
                        return "<a href='javascript:;' data-url='{$url}' class='_close'>关闭它</a>";
                    }
                    return false;
                },
                'preferential'=>function($url, $model, $key){
                    return \yii\helpers\Html::a('优惠',$url);
                }
            ]
        ]
    ],
]);?>

<script type="text/javascript">
    seajs.use('order',function(order){
        order.isPay();
        order.close();
    });
</script>
