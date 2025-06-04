# Aegis 🔐

Laravel包使用RSA和AES进行混合加密，支持安全的客户端-服务器通信和数字签名。

## 📦 安装

```bash
composer require layman/aegis
```

## ⚙️ 发布配置

```bash
php artisan vendor:publish --provider="Layman\Aegis\AegisServiceProvider" --tag=config
```

## 🛠️ 执行命令

```bash
php artisan aegis:generate
```

## 🚀 使用

```php
use Layman\Aegis\Facades\Aegis;

// 获取公钥（可用于客户端加密 AES 密钥）
Aegis::getPublicKey();
 // 解密
Aegis::decrypt($encrypted = "公钥加密的AES密钥", $nonce = "AES加密的随机数", $data = "AES加密数据");
// 签名
Aegis::signature($data = ["签名数据数组"]);
// 客户端验证签名
Aegis::clientVerifySignature($data = ["验签数据数组"], $signature = "服务端签名");
// 客户端加密
Aegis::clientEncryptData($data = ["需加密数据数组"]);
```

## 🙌 支持与贡献

欢迎提 Issue 或 PR 来改进此包。你的每一个建议和贡献，都是我们前进的动力！

如果你觉得 Aegis 有帮助，别忘了点个 ⭐ Star 哦！
