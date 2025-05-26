# 📦 KPF - CHANGELOG

모든 마이너 릴리즈는 프레임워크 핵심의 안정성과 기능 개선을 목표로 합니다.

## [v1.1.0] - 2022-12-09
### ✅ 주요 개선
- `Router` 클래스 `Application` 의존성 제거 및 DI 지원
- `Directory` 및 `Config` 개선: 테스트 용이성 및 구조 단순화
- `Installer`, `Maker` 리팩토링: DI 기반 구조 및 `make init` 지원
- 템플릿 오버라이드 기능 도입 (`.kpfconfig.json` 경로 기반)
- PHPUnit 유닛 테스트 구조 보강 및 높은 커버리지 확보

## [v1.0.1] - 2022-01-17
### 🔧 버그 수정 및 편의성 강화
- `Uri::get()` 슬래시 처리 로직 패치
- `Controller` 자동 템플릿 경로 추론 관련 버그 개선
- `Singleton` 클래스에 `resetInstance()` 메서드 추가로 테스트 격리 가능

## [v1.0.0] - 2022-01-10
### 🚀 초기 릴리즈
- Composer 설치: `composer require jonathanbak/kpf`
- 기본 MVC (`Application`, `Controller`, `Model`, `View`, `Config`, `Directory`) 제공
- `install.php` 스크립트로 프로젝트 구조 자동 생성
- 기본 라우팅 (`common.route.json`) 및 DB 설정 (`common.db.json`) 지원
- Twig 템플릿 엔진 (`Twig 3`) 통합