<?php namespace App;
/*
 * 分库分表的Model基类
 * 注意：使用时禁止调用底层的静态方法
 */

abstract class OrmPartition extends \Illuminate\Database\Eloquent\Model
{
    //默认的链接DB数据库
    protected $connection = null;

    //表名
    protected $table = null;

    /**
     * 设置主键字段
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * 设置创建时间字段
     */
    const CREATED_AT = 'create_time';

    /**
     * 设置更新时间字段
     */
    const UPDATED_AT = 'update_time';

    /**
     * 设置状态字段
     */
    const STATUS = 'is_delete';

    /**
     * 设置删除时间字段
     */
    const DELETED_AT = 'delete_time';

    /**
     * 标记是启用分库分表
     */
    const PARTITION_OPEN = 1;

    #软删除状态值 0:正常;1:删除
    const INVALID_STATUS = 1;

    /*
     * 分库分表参数值
     */
    protected $_partition_value = null;


    abstract public function setDBTable($partition, $partition_type = 0);

    public function __construct(array $attributes = [], $partition = 1, $partition_type = 1)
    {
        \App::make('database');
        parent::__construct($attributes);
        if (!empty($partition)) {
            $this->setDBTable($partition, $partition_type);
            $this->_partition_value = $partition;
        }
    }

    /*
     * 设置分库分表（使用静态方法时必须调用）
     *
     * @author wangkewei775
     * @param mixed $partition 分库分表参数值
     * @return object
     */
    public static function setPartition($user_id)
    {
        if (empty($user_id)) {
            throw new \Exception('设置分库分表参数错误');
        }
        $oModel = new static([], $user_id, 1);
        return $oModel;
    }

    // 通过seq获取分库分表数值
    private static function getPartitionNum($seq)
    {
        return intval(substr($seq, -3, 3));
    }

    /**
     * 根据序号分库分表
     * @param $seq  任意分库分表的 seq主健
     */
    public static function setPartitionBySeq($seq)
    {
        if (empty($seq) || strlen($seq) < 3) {
            throw  new  \Exception('设置分库分表seq格式错误');
        }

        $num    = self::getPartitionNum($seq);
        $oModel = new static([], $num, 0);
        return $oModel;
    }

    /**
     * @title: 通过多个seq查询唯一编码并分组
     * @param array $seqs 一个或多个seq
     * @return array [['seq_group' => [], 'model' => obj]]
     * @throws \Exception
     * @author: ganqixin <godfrey.gan@handeson.com>
     */
    public static function setPartitionBySeqGroup($seqs = [])
    {
        $seqs = is_array($seqs) ? $seqs : [$seqs];
        $data = [];
        foreach ($seqs as $seq) {
            $model                     = self::setPartitionBySeq($seq);
            $connection                = $model->connection;
            $table                     = $model->table;
            $key                       = sprintf("%s:%s", $connection, $table);
            $data[$key]['model']       = empty($data[$key]['model']) ? $model : $data[$key]['model'];
            $data[$key]['seq_group'][] = $seq;
        }
        return $data;
    }


    /**
     * 根据用户ID分库分表
     * @param user_id 用户ID
     */
    public static function setPartitionByUserId($user_id)
    {
        return self::setPartition($user_id);
    }

    /**
     * 列表查询
     * @param array $aCondition 条件 (非必需参数)
     *        可以使用的条件:'>','<','>=','<=','<>','!=','like',where,in，notin，between，notbetween，or，orderby
     *        使用方法见下面的例子；
     * 'aCondition' => [
     * 'sRecommendMobile' => '15262281953', //正常的where条件 sRecommendMobile = 15262281953；
     * '<'                => ['iLastLoginTime' => 14514717626], //代表iLastLoginTime<14514717626;
     * '<>'               => ['iUserID' => 47613, 'iLoginTimes' => 5],//代表iUserID<>47613,iLoginTimes<>5；
     * 'like'             => ['sName' => '%aaa'],//代表sName like %aaa；
     * 'between'          => ['iPayCenterBid' => [1, 10]],//代表 iPayCenterBid between 1 and 10；
     * 'notin'            => ['iCompanyID' => [1, 2]],//代表 not in (1,2)；
     * 'or'               => ['sRecommendMobile' => '15262281953'],//代表 or sRecommendMobile=15262281953；
     * 'orderby'          => ['iLastLoginTime' => 'desc'] //代表 order by iLastLoginTime desc；
     * ]
     * @param int $iPage 当前页
     * @param int $iPerPage 每页多少条
     * @param array $order 主要按哪个字段排序:如['iAutoID'=>'desc']
     * @param int $iLastID 当前页最后一条数据的ID
     * @param array $aColumn 要查询的字段
     * @return array
     */
    public function spList($aCondition = [], $iPage = 1, $iPerPage = 10, $order = [], $iLastID = 0, $aColumn = [])
    {
        //返回结果格式
        $aResult = [
            'total'     => 0,
            'page'      => $iPage,
            'page_size' => $iPerPage,
            'last_id'   => $iLastID,
            'list'      => [],
        ];
        //参数取值限制
        $iPage    = max(intval($iPage), 1);
        $iPerPage = min(max(intval($iPerPage), 1), 1000);
        $iLastID  = max(intval($iLastID), 0);

        //获取主键
        $primaryKey = isset($this->primaryKey) ? $this->primaryKey : 'iAutoID';
        $columns    = $this->columns;
        $oModel     = $this->getCondition($aCondition);
        //获取总数
        $aResult['total'] = $oModel->count();
        $res              = [];
        if ($aResult['total'] > 0) {
            //获取排序条件
            $order = !empty($order) && is_array($order) ? $order : [$primaryKey => 'desc'];
            foreach ($order as $k => $v) {
                if (in_array(strtolower($v), ['asc', 'desc'])) {
                    $oModel = $oModel->orderBy($k, $v);
                }
            }
            //如果设置要查询的字段 只查询要设置的字段，
            if (!empty($aColumn)) {
                $oModel = $oModel->select($aColumn);
            }
            //大数据分页
            if ($primaryKey && !empty($iLastID)) {
                $fh     = !empty($order[$primaryKey]) && ($order[$primaryKey] == 'asc') ? '>' : '<';
                $oModel = $oModel->where($primaryKey, $fh, $iLastID);
                $res    = $oModel->limit($iPerPage)->get()->toArray();
            } else { //常规分页
                $offset = ($iPage - 1) * $iPerPage;
                $res    = $oModel->skip($offset)->take($iPerPage)->get()->toArray();
            }
        }
        $aResult['list'] = $res;
        return $aResult;
    }

    /**
     * 处理条件参数
     * @param array $aCondition 筛选条件
     * 'aCondition'=>[
     * 'iType'   => 1,
     * '<'       => ['iLastLoginTime' => 14514717626],
     * '<>'      => ['iUserID' => 47613, 'iLoginTimes' => 5],
     * 'between' => ['iPayCenterBid' => [1, 10]],
     * 'notin'   => ['iCompanyID' => [1, 2]],
     * 'or'      => ['sRecommendMobile' => '15262281953'],
     * 'orderby' => ['iLastLoginTime' => 'desc']
     * ]
     * 可以使用的条件：where,in，notin，between，notbetween，or，orderby；后期可扩充
     * @return object
     */
    public function getCondition($aCondition)
    {
        if (empty($aCondition)) {
            return $this;
        }
        $oModel = $this;
        //where条件
        $where = ['>', '<', '>=', '<=', '<>', '!=', 'like', '&'];
        $other = [
            'in'         => 'whereIn',
            'notin'      => 'whereNotIn',
            'between'    => 'whereBetween',
            'notbetween' => 'whereNotBetween',
            'or'         => 'orWhere',
            'orderby'    => 'orderBy',
            'isnotnull'  => 'whereNotNull',
        ];
        foreach ($aCondition as $key => $val) {
            //键值对(eq情况)直接使用where条件，例如'sSketch'=>115551
            if (!is_array($val)) {
                $oModel = $oModel->where($key, $val);
            } else {
                //区间单值查询，例如'<' => ['iLastLoginTime' => 14514717626]
                if (in_array($key, $where)) {
                    //循环where条件
                    foreach ($val as $k => $v) {
                        $oModel = $oModel->where($k, $key, $v);
                    }
                } //区间数组查询，例如'between' => ['iPayCenterBid' => [1, 10]]
                elseif (in_array(strtolower($key), array_keys($other))) {
                    //循环where条件
                    if ($key == 'or') {
                        $oModel = $oModel->handleOr($val);
                    } else {
                        foreach ($val as $k => $v) {
                            $oModel = $oModel->{$other[strtolower($key)]}($k, $v);
                        }
                    }
                }
            }
        }
        return $oModel;
    }

    /**
     * desc：配合getCondition方法处理Or规则
     * @param array $val or规则数组
     * @return obj
     */
    private function handleOr($val)
    {
        return $this->where(function ($query) use ($val) {
            $j = 0;
            foreach ($val as $field => $value) {
                $j++;
                if ($j == 1) {
                    $query = $query->where($field, $value);
                } else {
                    $query = $query->orWhere($field, $value);
                }
            }
        });
    }

    /**
     * 按主键获取数据
     * @param $id   主键id
     * @return array
     */
    public function spGetByID($id)
    {
        return $this->find($id);
    }

    /**
     * 根据条件获取单条数据
     * @param $aCondition
     * @return bool
     */
    public function spGetOne($aCondition = [], $aColumn = [])
    {
        $oModel = $this->getCondition($aCondition);

        //如果设置要查询的字段 只查询要设置的字段，
        if (!empty($aColumn)) {
            $oModel = $oModel->select($aColumn);
        }
        return $oModel->first();
    }

    /**
     * 根据ID获取单条数据
     * @param $id
     * @param $columns
     * @return array
     */
    public function spFindOne($id, $columns)
    {
        $info = parent::find($id, $columns);
        return is_null($info) ? [] : $info->toArray();
    }

    /**
     * 根据条件获取所有数据
     * @param $aCondition
     * @return object
     */
    public function spGetAll($aCondition = [], $aColumn = [])
    {
        $oModel = $this->getCondition($aCondition);

        //如果设置要查询的字段 只查询要设置的字段，
        if (!empty($aColumn)) {
            $oModel = $oModel->select($aColumn);
        }

        return $oModel->get();
    }

    /**
     * 根据条件获取条数
     * @param $aCondition
     * @return int
     */
    public function spGetCount($aCondition = [])
    {
        return $this->getCondition($aCondition)->count();
    }

    /**
     * 按条件更新
     * @param array $aCondition 筛选条件
     * @param array $aData 更新数据
     * @return bool
     */
    public function spUpdate($aCondition = [], $aData = [])
    {
        if (!is_array($aCondition) || empty($aCondition) || !is_array($aData)) {
            return false;
        }
        return $this->getCondition($aCondition)->update($aData);
    }

    /**
     * 按主键更新
     * @param $id   主键
     * @param array $aData 更新数据
     * @return mixed
     */
    public function spUpdateByID($id, $aData = [])
    {
        return $this->where($this->primaryKey, $id)->update($aData);
    }

    /**
     * 插入记录
     * @param array $aData 需要添加的数据
     * @return bool
     */
    public function spAdd($aData = [])
    {
        foreach ($aData as $key => $val) {
            $this->$key = $val;
        }
        return $this->save();
    }

    /**
     * 插入记录 返回insertid
     * @param array $aData 需要添加的数据
     * @return array
     */
    public static function spStore($obj, $aData = [])
    {
        foreach ($aData as $key => $val) {
            $obj->$key = $val;
        }
        $obj->save();
        return $obj;
    }

    /**
     * 软删除数据
     * @param array $aCondition 筛选条件
     * @return mixed
     */
    public function spDelete($aCondition = [])
    {
        return $this->getCondition($aCondition)->delete();
    }

    /**
     * 获取当前时间
     * @return integer
     */
    public function freshTimestamp()
    {
        return time();
    }

    /**
     * 转换日期时间
     * @param  $mValue 日期时间
     * @return integer
     */
    public function fromDateTime($mValue)
    {
        return $mValue;
    }

    /**
     * 使用时间戳, 不自动格式化时间
     * @return array
     */
    public function getDates()
    {
        return [];
    }

    /**
     * 禁止静态调用
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     * @throws \Exception
     */
    public static function __callStatic($method, $parameters)
    {
        throw new \Exception(sprintf('方法%s禁止静态调用', $method));
    }

    public function insertGetId(array $values, $sequence = null)
    {
        self::fobbidenUse(__FUNCTION__);
    }

    protected static function fobbidenUse($method)
    {
        throw new \Exception(sprintf('方法%s禁止使用', $method));
    }

}
