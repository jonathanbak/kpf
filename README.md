# kpf
KPF 는 Kubernetes PHP Framework 의 약자로, 쿠버네티스 환경에서 쉽게 PHP MVC 개발을 가능하게 하기 위해 만들었습니다.
어려운 기교 없이 쉽게 접근하여 서비스에 집중 할 수 있기를 바랍니다.

Requirements
------------

requires the following:

* Composer - Dependency Management for PHP
* PHP 7.3 이상
* twig 3.x
* jonathanbak/mysqlilib 1.3

Installation
------------
 ```bash
 composer require jonathanbak/kpf
 ```

Start First Project
-------------
터미널에서 아래 스크립트를 실행하면 자동으로 폴더 구성 및 샘플 파일이 생성됩니다.
```shell
$ php ./vendor/jonathanbak/kpf/bin/install.php <namespace>
OK.
```

Folder Structure
-------------------
    .
    ├── controllers     # URL에서 접근하는 controller 파일
    ├── models          # 모델 파일, 주요 로직
    ├── views           # View 폴더, tpl 파일
    ├── config              # 설정 파일
    │   ├── common.route.json   # routing 파일
    │   └── common.db.json      # DB 정보 설정 파일
    ├── html                # 실제 웹서버의 DOCUMENT_ROOT
    │   ├── css             # css 파일
    │   ├── image           # images 파일
    │   ├── js              # javascript 파일
    │   └── index.php       # 웹 index 파일 , 모든 요청 포워딩
    ├── _tmp            # 임시 폴더, 캐쉬파일등
    ├── logs            # 로그 폴더
    ├── vendor              # Composer 라이브러리 폴더
    └── configure.json      # 메인 설정 파일
