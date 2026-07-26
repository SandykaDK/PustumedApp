// spec: tests/daftar-user-test-plan.md
// seed: tests/e2e/seed.spec.ts

import { test, expect } from '@playwright/test';

test.describe('Daftar-User', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('/login');
    await page.getByRole('textbox', { name: 'Email atau Username' }).fill('sandyka@gmail.com');
    await page.getByRole('textbox', { name: 'Password' }).fill('jarak123');
    await page.getByRole('button', { name: 'Login' }).click();
    await page.getByRole('link', { name: 'Daftar User' }).click();
  });

  // USER-001
  test('Halaman Daftar User tampil dengan data dan tombol utama', async ({ page }) => {
    await expect(page.getByRole('heading', { name: 'Daftar User' })).toBeVisible();
    await expect(page.getByRole('table')).toBeVisible();
    await expect(page.getByRole('button', { name: 'Tambah' })).toBeVisible();
  });

  // USER-002
  test('User dapat mencari data berdasarkan nama, email, atau nomor telepon', async ({ page }) => {
    await page.getByRole('textbox', { name: /Cari nama, email, atau no/i }).fill('User 1');

    await expect(page.getByRole('cell', { name: 'User 1' })).toBeVisible();
    await expect(page.getByRole('cell', { name: 'sandyka@gmail.com' })).toBeVisible();
  });

  // USER-003
  test('User dapat memfilter data berdasarkan status aktif atau nonaktif', async ({ page }) => {
    await page.locator('select[name="status"]').selectOption('aktif');

    const userRow = page.locator('tbody tr').filter({ has: page.locator('td').filter({ hasText: 'User 1' }) }).first();

    await expect(userRow).toBeVisible();
    await expect(userRow.locator('td').nth(5)).toContainText('Aktif');
  });

  // USER-004
  test('User dapat mereset filter pencarian dan status', async ({ page }) => {
    await page.getByRole('textbox', { name: /Cari nama, email, atau no/i }).fill('User 1');
    await page.locator('select[name="status"]').selectOption('aktif');

    await page.waitForTimeout(700);
    await page.getByRole('link', { name: 'Reset' }).click();

    await page.waitForURL(/\/users(?:$|\?)/);

    const filterForm = page.locator('form.filter-form');
    await expect(filterForm.getByRole('textbox', { name: /Cari nama, email, atau no/i })).toHaveValue('');
    await expect(filterForm.locator('select[name="status"]')).toHaveValue('');

    await expect(page.getByRole('cell', { name: 'User 2' })).toBeVisible();
    await expect(page.getByRole('cell', { name: 'User 3' })).toBeVisible();
  });

  test('User dapat membuka modal tambah dan membuat user baru', async ({ page }) => {
    const uniqueSuffix = Date.now();
    const createModal = page.locator('#createUserModal');

    await page.getByRole('button', { name: 'Tambah' }).click();

    await expect(page.getByRole('heading', { name: 'Tambah User' })).toBeVisible();
    await expect(createModal.getByLabel('Nama')).toBeVisible();
    await expect(createModal.getByLabel('Email')).toBeVisible();
    await expect(createModal.getByLabel('Role')).toBeVisible();
    await expect(createModal.locator('#password')).toBeVisible();
    await expect(createModal.locator('#password_confirmation')).toBeVisible();

    await createModal.getByLabel('Nama').fill(`Test ${uniqueSuffix}`.slice(0, 12));
    await createModal.getByLabel('Email').fill(`testuser@example.com`);
    await createModal.getByLabel('No Telepon').fill(`081234567${uniqueSuffix}`.slice(0, 13));
    await createModal.getByLabel('Role').selectOption('petugas_obat');
    await createModal.locator('#password').fill('Password123!');
    await createModal.locator('#password_confirmation').fill('Password123!');
    await createModal.getByRole('button', { name: 'Simpan' }).click();

    await expect(page.getByText('User berhasil ditambahkan')).toBeVisible();
    await expect(page.getByRole('cell', { name: `Test ${uniqueSuffix}` .slice(0, 12) })).toBeVisible();
  });

  // USER-005
  test('Validasi form tambah user', async ({ page }) => {
    const createModal = page.locator('#createUserModal');

    await page.getByRole('button', { name: 'Tambah' }).click();
    await createModal.getByRole('button', { name: 'Simpan' }).click();

    await expect(createModal.locator('.error-list')).toBeVisible();
    await expect(createModal.locator('.error-list')).toContainText(/harus diisi/i);
    await expect(page.getByRole('heading', { name: 'Tambah User' })).toBeVisible();
  });

  // USER-006
  test('User dapat membuka modal edit dan mengubah data user', async ({ page }) => {
    const updatedName = `User Updated ${Date.now()}`;
    const editModal = page.locator('#editUserModal');

    await page.locator('tr').filter({ hasText: 'User 2' }).locator('.openEditModal').click();

    await expect(page.getByRole('heading', { name: 'Edit User' })).toBeVisible();
    await expect(editModal.getByLabel('Nama')).toHaveValue(/User/);

    await editModal.getByLabel('Nama').fill(updatedName);
    await editModal.getByRole('button', { name: 'Simpan' }).click();

    await expect(page.getByText('User berhasil diperbarui')).toBeVisible();
    await expect(page.getByRole('cell', { name: updatedName })).toBeVisible();
  });

  // USER-007
  test('User dapat menghapus user melalui konfirmasi hapus', async ({ page }) => {
    // Data Preparation
    const createModal = page.locator('#createUserModal');
    const deleteData = 'Test-Delete';

    await page.getByRole('button', { name: 'Tambah' }).click();

    await createModal.getByLabel('Nama').fill(deleteData);
    await createModal.getByLabel('Email').fill(`delete@example.com`);
    await createModal.getByLabel('No Telepon').fill(`081238728766`);
    await createModal.getByLabel('Role').selectOption('petugas_administrasi');
    await createModal.locator('#password').fill('Password123!');
    await createModal.locator('#password_confirmation').fill('Password123!');
    await createModal.getByRole('button', { name: 'Simpan' }).click();

    // Delete
    await page.locator('tr').filter({ hasText: deleteData }).locator('.action-btn.delete').click();

    const deleteDialog = page.locator('.confirm-action-modal.open');

    await expect(deleteDialog).toBeVisible();
    await expect(deleteDialog.getByText(/Yakin ingin menghapus user/i)).toBeVisible();
    await deleteDialog.getByRole('button', { name: 'Hapus' }).click();

    await expect(page.getByText('User berhasil dihapus')).toBeVisible();
    await expect(page.getByRole('cell', { name: deleteData })).not.toBeVisible();
  });
});
