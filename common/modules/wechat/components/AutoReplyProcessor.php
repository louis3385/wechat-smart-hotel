<?php

namespace common\modules\wechat\components;

use common\modules\wechat\models\WxPlatform;
use yii\helpers\Json;

/**
 * 自动回复处理器
 * Class AutoReplyProcessor
 */
class AutoReplyProcessor
{

    /**
     * @var WxPlatform 公众账号
     */
    private $_platform;

    /**
     * @var array 原公众号的自动回复JSON
     */
    private $_replyInfo;

    /**
     * Constructor
     * @param WxPlatform $platform
     */
    public function __construct($platform)
    {
        $this->_platform = $platform;
        $this->_replyInfo = Json::decode($platform->autoReply->content);
    }

    /**
     * 根据自动回复规则生成回复
     * @param array $msg
     * @return array|null
     */
    public function autoReply($msg)
    {
        $replyInfo = $this->_replyInfo;
        switch ($msg['MsgType']) {
            case 'text':
                $keyword = $msg['Content'];
                if (empty($keyword)) {
                    return null;
                }
                if (isset($replyInfo['keyword_autoreply_info'])) {
                    $rules = $replyInfo['keyword_autoreply_info']['list'];
                    foreach ($rules as $rule) {
                        $keywordCandidates = $rule['keyword_list_info'];
                        $keywordMatch = false;
                        foreach ($keywordCandidates as $kc) {
                            if (($kc['match_mode'] == 'contain' && strpos($keyword, $kc['content']) !== false) ||
                                ($kc['match_mode'] == 'equal' && $kc['content'] == $keyword)
                            ) {
                                $keywordMatch = true;
                                break;
                            }
                        }
                        if ($keywordMatch) {
                            $replies = $rule['reply_list_info'];
                            if (count($replies) > 1 && $rule['reply_mode'] == 'random_one') {
                                $index = array_rand($replies);
                                return $replies[$index];
                            }
                            return $replies[0];
                        }
                    }
                }
                // 没有设置关键词自动回复或关键词不匹配
                if (isset($replyInfo['message_default_autoreply_info'])) {
                    return $replyInfo['message_default_autoreply_info'];
                }
                break;
            case 'event':
                if ($msg['Event'] == 'subscribe') {
                    if (isset($replyInfo['add_friend_autoreply_info'])) {
                        return $replyInfo['add_friend_autoreply_info'];
                    }
                }
                break;
        }
        return null;
    }

}