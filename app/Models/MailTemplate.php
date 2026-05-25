<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailTemplate extends Model
{
    protected $table = 'mail_templates';
    protected $guarded = ['id'];

    public static function getTemplate(string $type): ?self
    {
        return static::where('type', $type)->where('is_active', true)->first();
    }

    public function render(array $variables = []): array
    {
        $subject = $this->subject;
        $body    = $this->body;

        foreach ($variables as $key => $value) {
            $subject = str_replace('{' . $key . '}', $value, $subject);
            $body    = str_replace('{' . $key . '}', $value, $body);
        }

        return compact('subject', 'body');
    }
}
