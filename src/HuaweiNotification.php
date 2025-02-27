<?php

namespace Kiddocare\LaravelHuaweiPush;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Opis\Closure\SerializableClosure;

class HuaweiNotification extends Notification implements ShouldQueue
{
    use Queueable,
        WithHuaweiNotification;

    protected $message = [];

    /**
     * @var Notifiable
     */
    protected $notifiable;

    /**
     * @var \Closure|SerializableClosure
     */
    protected $handler;

    public function __construct($message = [])
    {
        $this->message = $message;
    }

    public function title($set = null)
    {
        return $this->getOrSet(__FUNCTION__,$set);
    }

    public function description($set = null)
    {
        return $this->getOrSet(__FUNCTION__,$set);
    }

    public function body($set = null)
    {
        return $this->getOrSet(__FUNCTION__,$set);
    }

    public function content($set = null)
    {
        if(isset($set))
        {
            $this->body($set);
            $this->description($set);
            return $this;
        }
        return $this->body() ?: $this->description();
    }

    public function payload($set = null)
    {
        return $this->getOrSet(__FUNCTION__,$set);
    }

    protected function getOrSet($key,$val = null)
    {
        if(isset($val))
        {
            data_set($this->message,$key,$val);
            return $this;
        }
        return data_get($this->message,$key);
    }

    /**
     * 推送频道
     */
    public function via($notifiable)
    {
        $cls = [];
        $this->notifiable = $notifiable;
        if(is_object($notifiable) && method_exists($notifiable,'routeNotificationFor'))
        {
            if($notifiable->routeNotificationFor('huaweiPush'))
            {
                $cls[] = 'huawei_push';
            }
        }
        return $cls;
    }

    /**
     * 处理消息格式
     */
    public function setHandler(\Closure $fun)
    {
        $this->handler = new SerializableClosure($fun);
        return $this;
    }

    public function toMsg($notifiable = null)
    {
        return $this->message;
    }

    public function toArray($notifiable = null)
    {
        return $this->message;
    }

}
