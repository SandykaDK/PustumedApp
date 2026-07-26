// spec: tests/login-test-plan.md
// seed: tests/e2e/seed.spec.ts

import { test, expect } from '@playwright/test';

test.describe('Login', () => {
  test('Login berhasil dengan akun aktif', async ({ page }) => {
    await page.goto('/login');

    const identifierInput = page.getByRole('textbox', { name: 'Email atau Username' });
    const passwordInput = page.getByRole('textbox', { name: 'Password' });
    const loginButton = page.getByRole('button', { name: 'Login' });

    await identifierInput.fill('sandyka@gmail.com');
    await passwordInput.fill('jarak123');
    await loginButton.click();

    await expect(page).toHaveURL(/\/dashboard/);
    await expect(page.getByRole('heading', { name: /Dashboard/i })).toBeVisible();
  });

  test('Login gagal dengan kredensial salah', async ({ page }) => {
    await page.goto('/login');

    const identifierInput = page.getByRole('textbox', { name: 'Email atau Username' });
    const passwordInput = page.getByRole('textbox', { name: 'Password' });
    const loginButton = page.getByRole('button', { name: 'Login' });

    await identifierInput.fill('notfound@example.com');
    await passwordInput.fill('wrongpass');
    await loginButton.click();

    await expect(page.getByText('Email/username atau password')).toBeVisible();
    await expect(page).toHaveURL(/\/login/);
  });

  test('Validasi field wajib kosong', async ({ page }) => {
    await page.goto('/login');

    const loginButton = page.getByRole('button', { name: 'Login' });

    await loginButton.click();

    await expect(page.getByRole('textbox', { name: 'Email atau Username' })).toBeVisible();
    await expect(page).toHaveURL(/\/login/);
  });

  test('Login dengan username aktif', async ({ page }) => {
    await page.goto('/login');

    const identifierInput = page.getByRole('textbox', { name: 'Email atau Username' });
    const passwordInput = page.getByRole('textbox', { name: 'Password' });
    const loginButton = page.getByRole('button', { name: 'Login' });

    await identifierInput.fill('User 1');
    await passwordInput.fill('jarak123');
    await loginButton.click();

    await expect(page).toHaveURL(/\/dashboard/);
    await expect(page.getByRole('heading', { name: /Dashboard/i })).toBeVisible();
  });

  test('Login dengan akun nonaktif ditolak', async ({ page }) => {
    await page.goto('/login');

    const identifierInput = page.getByRole('textbox', { name: 'Email atau Username' });
    const passwordInput = page.getByRole('textbox', { name: 'Password' });
    const loginButton = page.getByRole('button', { name: 'Login' });

    await identifierInput.fill('nonaktif@example.com');
    await passwordInput.fill('jarak123');
    await loginButton.click();

    await expect(page.getByText('Email/username atau password')).toBeVisible();
    await expect(page).toHaveURL(/\/login/);
  });

  test('Akses dashboard tanpa login ditolak', async ({ page }) => {
    await page.goto('/dashboard');

    await expect(page).toHaveURL(/\/login/);
  });
});
