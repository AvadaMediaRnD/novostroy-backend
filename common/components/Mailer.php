<?php


namespace common\components;


use yii\swiftmailer\Mailer as MailerBase;

class Mailer extends MailerBase
{

    /**
     * @param string $email
     * @param string $from
     * @param string $to
     * @param string $flatName
     * @return bool
     */
    public function reservationActivated(string $email, string $from, string $to, string $flatName): bool
    {

        try {
            return (bool)$this->compose()
                ->setTo($email)
                ->setSubject('Заявка активна')
                ->setHtmlBody('<h1> Ваша заявка на квартиру активна c ' .
                    $from . ' по ' . $to .
                    ' </h1><br><p>' . $flatName . '</p>')
                ->send();
        } catch (\Throwable $e) {
            \Yii::error($e->getMessage() . $e->getFile() . $e->getLine(), __METHOD__);
        }

        return false;

    }

    /**
     * @param string $email
     * @param string $from
     * @param string $to
     * @param string $flatInfo
     * @return bool
     */
    public function preemptActivated(string $email, string $from, string $to, string $flatInfo): bool
    {

        try {
            return (bool)$this->compose()
                ->setTo($email)
                ->setSubject('Ваш резерв активен')
                ->setHtmlBody('<h1>Ваш резерв активен c ' .
                    $from . ' по ' . $to .
                    ' </h1><br><p>' . $flatInfo . '</p>')
                ->send();
        } catch (\Throwable $e) {
            \Yii::error($e->getMessage() . $e->getFile() . $e->getLine(), __METHOD__);
        }

        return false;

    }
}