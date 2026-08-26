import 'package:flutter/foundation.dart';
import 'screen.dart';

/// Hand-rolled navigation stack machine, ported from App.tsx's
/// navigate/goBack/resetTo/switchTab design (NavFrame[] stack).
class NavController extends ChangeNotifier {
  final List<NavFrame> _stack = [const NavFrame(AppScreen.splash)];
  bool offline = false;

  NavFrame get current => _stack.last;
  bool get canGoBack => _stack.length > 1;

  void navigate(AppScreen screen, [NavParams? params]) {
    _stack.add(NavFrame(screen, params));
    notifyListeners();
  }

  void goBack() {
    if (_stack.length > 1) {
      _stack.removeLast();
      notifyListeners();
    }
  }

  void resetTo(AppScreen screen, [NavParams? params]) {
    _stack
      ..clear()
      ..add(NavFrame(screen, params));
    notifyListeners();
  }

  /// Bottom-nav taps clear back-history, same as switchTab in App.tsx.
  void switchTab(AppScreen screen) => resetTo(screen);

  void toggleOffline() {
    offline = !offline;
    notifyListeners();
  }

  /// Driven by a real periodic `/health` check (see main.dart) rather than
  /// manual toggling — a no-op if the value hasn't changed, so a healthy
  /// connection doesn't spam listeners every check interval.
  void setOffline(bool value) {
    if (offline == value) return;
    offline = value;
    notifyListeners();
  }
}
