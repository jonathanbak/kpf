# KPF - Kubernetes Friendly PHP Framework

[![Build Status](https://github.com/jonathanbak/mysqlilib/actions/workflows/test.yml/badge.svg)](https://github.com/jonathanbak/kpf/actions/workflows/test.yml)
[![codecov](https://codecov.io/gh/jonathanbak/mysqlilib/branch/master/graph/badge.svg)](https://codecov.io/gh/jonathanbak/kpf)
![PHP Version](https://img.shields.io/badge/php-7.2%20~%208.3-blue)

**KPF (Kubernetes Friendly PHP Framework)**는 PHP 환경에서 MVC 아키텍처 기반 웹 애플리케이션을 간편하게 구축할 수 있도록 설계된 **경량 독립형 마이크로 프레임워크**입니다.

웹서버 설정에 종속되지 않고, 프레임워크 레벨에서 경로와 네임스페이스를 동적으로 바인딩할 수 있어 **Kubernetes와 같은 컨테이너 환경뿐 아니라, 로컬 개발, 서버 호스팅 등 다양한 배포 환경에서 유연하게 작동**합니다.

Kubernetes 환경에 친화적이되, 특정 인프라에 얽매이지 않는 **확장성과 독립성을 동시에 지향합니다**.

## Features

- **MVC 아키텍처 기반**: 컨트롤러, 모델, 뷰 구조로 명확한 책임 분리
- **Kubernetes 친화적 설계**: 웹서버 설정 없이 프레임워크 내부 라우팅으로 유연한 서비스 구성 가능
- **웹/CLI 환경 모두 지원**: 웹 요청뿐 아니라 커맨드라인 인터페이스에서도 동일한 컨트롤러 로직 재사용 가능
- **프레임워크 디렉토리 유연성**: `configure.json` 설정만으로 디렉토리 구조와 네임스페이스를 자유롭게 정의
- **의존성 최소화, 독립형 구조**: 특정 웹서버, 플랫폼, 호스팅에 종속되지 않음
- **Twig 3 지원**: 깔끔하고 강력한 템플릿 시스템 제공
- *PHP 7.2 이상 호환**: 최신 기능은 활용하면서도 안정적인 버전 호환성 유지

---

## ✅ Requirements

- PHP >= 7.2.5
- Composer
- twig 3.11.x (`~3.11` 권장)
- `jonathanbak/mysqlilib:^1.3`

---

## 📦 설치

```bash
composer require jonathanbak/kpf
```

---

## 🚀 첫 프로젝트 시작

```bash
php ./vendor/jonathanbak/kpf/bin/install.php <YourNamespace>
```

해당 명령을 실행하면 기본적인 폴더 구조와 샘플 Controller, Model, View, Route 설정 등이 자동으로 생성됩니다.

---

## 🛠 커맨드로 컴포넌트 자동 생성

```bash
php ./vendor/jonathanbak/kpf/bin/make.php controller Main/Home
php ./vendor/jonathanbak/kpf/bin/make.php model Account/User
php ./vendor/jonathanbak/kpf/bin/make.php view home/index
php ./vendor/jonathanbak/kpf/bin/make.php page home/index
```

- `controller`, `model`, `view`, `page` 생성 가능
- `page`는 controller + view 동시 생성
- `--force` 옵션을 추가하면 기존 파일을 덮어씀

---

## 🧪 테스트 CLI 실행 예시

```bash
php ./html/index.php /_sys/Test/main?type=test
```

---

## 📂 폴더 구조

```
.
├── controllers/              # URL에서 호출되는 Controller 파일
├── models/                   # DB/비즈니스 로직을 담는 Model 클래스
├── views/                    # Twig 템플릿 파일
├── config/                   # 환경별 JSON 설정파일
│   ├── common.route.json     # 라우팅 정보
│   └── common.db.json        # DB 접속 정보
├── html/                     # 웹서버 루트 (index.php)
│   ├── css/
│   ├── js/
│   ├── images/
│   └── index.php             # 모든 요청의 진입점
├── _tmp/                     # 컴파일 캐시, 세션 등 임시 파일
├── logs/                     # 로그 파일
├── vendor/                   # Composer 종속성
└── configure.json            # 메인 설정 파일
```

---

## 🛠️ Initialize Custom Templates (`make init`)

KPF는 모듈(Controller, Model, View 등)을 손쉽게 생성할 수 있도록 `make.php`를 제공합니다.  
개발자가 **커스터마이징 가능한 템플릿**을 사용하도록 하기 위해, 초기 설정을 아래와 같이 진행할 수 있습니다:

```bash
php ./vendor/jonathanbak/kpf/bin/make.php init
```

이 명령은 다음과 같은 작업을 수행합니다:

- `.kpfconfig.json` 파일을 생성하여 설정 기반을 만듭니다.
- `kpf-resource/` 폴더를 생성하고, 필요한 템플릿 파일만 복사합니다:
    - `controllers/Blank.php.txt`
    - `models/Blank.php.txt`
    - `views/blank.twig.txt`

설정 파일은 아래와 같이 구성됩니다:

```json
{
  "resourcePath": "kpf-resource",
  "destDirs": {
    "controller": "controllers",
    "model": "models",
    "view": "views"
  }
}
```

---

## 📁 `kpf-resource` 구조 및 활용

템플릿 수정 시 `kpf-resource/` 폴더 내 파일을 편집하면 됩니다.  
모듈 생성 시 `.kpfconfig.json`에서 지정한 템플릿 파일이 우선 적용됩니다.

```
kpf-resource/
├── controllers/
│   └── Blank.php.txt     # Controller 템플릿
├── models/
│   └── Blank.php.txt     # Model 템플릿
└── views/
    └── blank.twig.txt    # View 템플릿
```

---

## ✨ 모듈 자동 생성 예시

```bash
php ./vendor/jonathanbak/kpf/bin/make.php model Sample
php ./vendor/jonathanbak/kpf/bin/make.php view sample
php ./vendor/jonathanbak/kpf/bin/make.php page Demo/Welcome
```

- `page`는 controller + view를 동시에 생성합니다.
- `.kpfconfig.json`에 따라 템플릿과 생성 경로가 자동 적용됩니다.

## 📝 라이센스

MIT License
