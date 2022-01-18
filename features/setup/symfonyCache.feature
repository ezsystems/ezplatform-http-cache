@setup
Feature: Set up the system to use Symfony Proxy

  Scenario: Set up the system to use Symfony Proxy
    Given I apply the patch
"""
From fe9ff7bd801f978cfc50aae0411bb3eefdd3059a Mon Sep 17 00:00:00 2001
From: =?UTF-8?q?Marek=20Noco=C5=84?= <mnocon@users.noreply.github.com>
Date: Wed, 7 Jul 2021 15:29:57 +0200
Subject: [PATCH] Enabled Symfony Reverse Proxy

---
 public/index.php | 7 ++++++-
 1 file changed, 6 insertions(+), 1 deletion(-)

diff --git a/public/index.php b/public/index.php
index 9982c21..03ac40a 100644
--- a/public/index.php
+++ b/public/index.php
@@ -1,9 +1,14 @@
 <?php
 
 use App\Kernel;
+use EzSystems\PlatformHttpCacheBundle\AppCache;
+use Symfony\Component\HttpFoundation\Request;
 
 require_once dirname(__DIR__).'/vendor/autoload_runtime.php';
 
 return function (array $context) {
-    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
+    $kernel = new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
+    Request::enableHttpMethodParameterOverride();
+
+    return new AppCache($kernel);
 };
-- 
2.30.0
"""
