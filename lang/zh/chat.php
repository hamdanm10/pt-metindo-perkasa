<?php

declare(strict_types=1);

return [
    'open_chat' => '打开聊天助手',
    'open_whatsapp' => '通过 WhatsApp 联系客服',
    'close' => '关闭',
    'chat' => [
        'title' => '在线咨询',
        'subtitle' => '快速了解 PT Metindo Perkasa',
        'placeholder' => '输入您的问题...',
        'send' => '发送',
        'welcome' => '您好！我可以回答常见问题。请选择下面的话题，或直接输入问题。',
        'fallback' => '抱歉，我暂时无法回答这个问题。请选择下面的话题，或联系我们的团队。',
        'topics' => '话题',
        'you' => '您',
        'bot' => '助手',
    ],
    'whatsapp' => [
        'title' => 'WhatsApp 客服',
        'hint' => '请选择一个号码开始聊天。',
        'chat' => '聊天',
        'default_name' => '客服',
        'message' => '您好 PT Metindo Perkasa，我想咨询贵公司的服务。',
    ],
    'topics' => [
        'processes' => [
            'label' => '工艺',
            'answer' => '我们提供 :count 个类别的电镀工艺，例如镀锌、镀镍、镀铜、硬铬和镀锡，以及阳极氧化和磷化。',
            'link' => '查看全部工艺',
            'keywords' => ['工艺', '电镀', '镀锌', '镀镍', '镀铜', '镀铬', '镀锡', '阳极', '磷化', '抛光', '类别'],
        ],
        'services' => [
            'label' => '服务',
            'answer' => '我们提供电镀服务、电镀化学品供应和技术咨询。',
            'link' => '查看我们的服务',
            'keywords' => ['服务', '提供', '供应', '咨询', '化学品'],
        ],
        'quote' => [
            'label' => '咨询建议',
            'answer' => '请告诉我们工艺和基材，我们的团队会为您介绍可行的方案。',
            'link' => '联系我们',
            'keywords' => ['报价', '价格', '费用', '建议', '规格', '帮助', '订购', '询价'],
        ],
        'contact' => [
            'label' => '联系方式',
            'email' => '邮箱：:email。',
            'phone' => '电话：:phone。',
            'link' => '打开联系页面',
            'keywords' => ['联系', '邮箱', '电话', '联络'],
        ],
        'hours' => [
            'label' => '营业时间',
            'answer' => '我们的营业时间：:hours。',
            'link' => '打开联系页面',
            'keywords' => ['时间', '营业', '开放', '几点', '何时'],
        ],
        'location' => [
            'label' => '位置',
            'answer' => '地址：:address',
            'link' => '查看我们的子公司',
            'keywords' => ['地址', '位置', '在哪', '地图', '城市', '子公司', '办公'],
        ],
        'company' => [
            'label' => '关于公司',
            'answer' => 'PT Metindo Perkasa 是印度尼西亚的金属表面处理与电镀专家。',
            'link' => '关于我们',
            'keywords' => ['关于', '公司', '简介', '谁'],
        ],
    ],
];
