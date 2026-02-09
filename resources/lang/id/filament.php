<?php

return [
    'login' => [
        'heading' => 'Masuk ke akun Anda',
        'buttons' => [
            'submit' => [
                'label' => 'Masuk',
            ],
        ],
        'fields' => [
            'email' => [
                'label' => 'Email',
            ],
            'password' => [
                'label' => 'Password',
            ],
            'remember' => [
                'label' => 'Ingat saya',
            ],
        ],
        'messages' => [
            'failed' => 'Kredensial tidak cocok dengan data kami.',
            'throttled' => 'Terlalu banyak percobaan login. Silakan coba lagi dalam :seconds detik.',
        ],
    ],

    'buttons' => [
        'cancel' => [
            'label' => 'Batal',
        ],
        'create' => [
            'label' => 'Buat',
        ],
        'delete' => [
            'label' => 'Hapus',
        ],
        'edit' => [
            'label' => 'Ubah',
        ],
        'save' => [
            'label' => 'Simpan',
        ],
        'view' => [
            'label' => 'Lihat',
        ],
    ],

    'fields' => [
        'search_query' => [
            'label' => 'Cari',
            'placeholder' => 'Cari...',
        ],
    ],

    'pagination' => [
        'label' => 'Navigasi halaman',
        'overview' => 'Menampilkan :first sampai :last dari :total hasil',
        'fields' => [
            'records_per_page' => [
                'label' => 'per halaman',
            ],
        ],
        'buttons' => [
            'go_to_page' => [
                'label' => 'Ke halaman :page',
            ],
            'next' => [
                'label' => 'Berikutnya',
            ],
            'previous' => [
                'label' => 'Sebelumnya',
            ],
        ],
    ],

    'tables' => [
        'empty' => [
            'heading' => 'Tidak ada data',
            'description' => 'Belum ada data untuk ditampilkan.',
        ],
        'filters' => [
            'heading' => 'Filter',
            'buttons' => [
                'remove' => [
                    'label' => 'Hapus filter',
                ],
                'remove_all' => [
                    'label' => 'Hapus semua filter',
                ],
            ],
        ],
        'actions' => [
            'modal' => [
                'buttons' => [
                    'cancel' => [
                        'label' => 'Batal',
                    ],
                    'confirm' => [
                        'label' => 'Konfirmasi',
                    ],
                ],
            ],
        ],
    ],

    'notifications' => [
        'database' => [
            'modal' => [
                'heading' => 'Notifikasi',
                'buttons' => [
                    'clear' => [
                        'label' => 'Hapus',
                    ],
                    'mark_all_as_read' => [
                        'label' => 'Tandai semua sudah dibaca',
                    ],
                ],
                'empty' => [
                    'heading' => 'Tidak ada notifikasi',
                    'description' => 'Silakan periksa kembali nanti.',
                ],
            ],
        ],
    ],
];
