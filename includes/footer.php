<?php if ($is_login_page): ?>
        </div>
<?php elseif (!$is_admin): ?>
        </main>
        
        <!-- Ghibli Twilight Forest Footer - SOFT DARK THEME -->
        <footer class="ghibli-footer">
            <div class="container">
                <div class="row">
                    <div class="col-md-4 mb-4 mb-md-0">
                        <div class="footer-brand">
                            <div class="brand-icon">
                                <img src="<?= ASSETS_URL ?>/images/totoro.png" alt="Totoro" class="brand-totoro" loading="lazy">
                            </div>
                            <h6 class="brand-name">Kelurahan Sasi</h6>
                        </div>
                        <p class="brand-description" style="color: #FFFFFF !important;">
                            Sistem informasi kelurahan untuk layanan surat dan informasi publik yang transparan dengan nuansa pedesaan yang hangat dan modern.
                        </p>
                    </div>
                    
                    <div class="col-md-4 mb-4 mb-md-0">
                        <h6 class="footer-title">Navigasi</h6>
                        <ul class="footer-nav">
                            <li><a href="<?= PUBLIC_URL ?>/index.php">Beranda</a></li>
                            <li><a href="<?= PUBLIC_URL ?>/profil-desa.php">Profil Kelurahan</a></li>
                            <li><a href="<?= PUBLIC_URL ?>/layanan_surat.php">Layanan Surat</a></li>
                            <li><a href="<?= PUBLIC_URL ?>/lacak_permohonan.php">Lacak Permohonan</a></li>
                        </ul>
                    </div>
                    
                    <div class="col-md-4 mb-4 mb-md-0">
                        <h6 class="footer-title">Kontak & Layanan</h6>
                        <ul class="footer-contact">
                            <li class="contact-item">
                                <span class="contact-icon">📍</span>
                                <span class="contact-text">Jl. Merdeka No. 24, Kefamenanu</span>
                            </li>
                            <li class="contact-item">
                                <span class="contact-icon">📞</span>
                                <span class="contact-text">(0380) 123456</span>
                            </li>
                            <li class="contact-item">
                                <span class="contact-icon">✉️</span>
                                <span class="contact-text">info@kelurahansasi.id</span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="footer-divider"></div>
                
                <div class="footer-bottom">
                    <div class="copyright">
                        &copy; <?= date('Y') ?> Kelurahan Sasi. Seluruh hak cipta dilindungi undang-undang.
                    </div>
                    <div class="footer-motto">
                        <span class="motto-text">Melayani dengan hati, bekerja dengan integritas</span>
                    </div>
                </div>
            </div>
        </footer>
        
        <style>
        /* ============================================
           FOOTER - GHIBLI TWILIGHT FOREST THEME
           Soft dark colors like dusk in the forest
           ============================================ */
        
        /* Soft dark gradient - like twilight over a Ghibli forest */
        .ghibli-footer,
        footer.ghibli-footer,
        body .ghibli-footer,
        footer {
            background: linear-gradient(135deg, #2A3B2C 0%, #1E2A22 30%, #25382B 70%, #1F2E23 100%) !important;
            background-color: #243428 !important;
            background-image: url('<?= ASSETS_URL ?>/images/rice-fields-pattern.png') !important;
            background-repeat: repeat !important;
            background-position: center !important;
            background-blend-mode: soft-light !important;
            background-size: 60px !important;
            opacity: 1 !important;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
            border: none !important;
            border-top: 2px solid rgba(160, 140, 110, 0.5) !important;
            position: relative !important;
            z-index: 100 !important;
            margin-top: 4rem !important;
            padding: 3rem 0 2rem !important;
            box-shadow: 0 -8px 20px rgba(0, 0, 0, 0.2) !important;
        }
        
        /* Decorative leaf silhouette effect */
        .ghibli-footer::before {
            content: '' !important;
            display: block !important;
            position: absolute !important;
            top: -25px !important;
            left: 0 !important;
            right: 0 !important;
            height: 25px !important;
            background: linear-gradient(135deg, 
                        transparent 0%, transparent 45%,
                        rgba(90, 80, 60, 0.3) 45%, rgba(90, 80, 60, 0.3) 50%,
                        transparent 50%, transparent 55%,
                        rgba(90, 80, 60, 0.3) 55%, rgba(90, 80, 60, 0.3) 60%,
                        transparent 60%) !important;
            background-size: 30px 25px !important;
            background-repeat: repeat-x !important;
            opacity: 0.4 !important;
            pointer-events: none !important;
        }
        
        /* Remove other pseudo-elements */
        .ghibli-footer::after,
        footer.ghibli-footer::after,
        body .ghibli-footer::after,
        footer::after {
            display: none !important;
            content: none !important;
        }
        
        /* Ensure footer content is visible */
        .ghibli-footer .container,
        footer .container {
            position: relative !important;
            z-index: 10 !important;
        }
        
        /* Soft warm text colors */
        .ghibli-footer,
        .ghibli-footer *,
        footer,
        footer * {
            color: #F0EDE5 !important;
        }
        
        .ghibli-footer .footer-title,
        footer .footer-title {
            color: #C7D1B5 !important;
            font-weight: 500 !important;
            margin-bottom: 1.25rem !important;
            font-size: 1rem !important;
            letter-spacing: 1px !important;
            text-transform: uppercase !important;
            opacity: 0.85 !important;
        }
        
        .ghibli-footer .brand-name,
        footer .brand-name {
            color: #E6E0D0 !important;
            font-weight: 600 !important;
            font-size: 1.2rem !important;
            margin: 0 !important;
        }
        
        .ghibli-footer .footer-nav li a,
        footer .footer-nav li a {
            color: #D8D0BC !important;
            text-decoration: none;
            transition: all 0.2s ease !important;
            display: inline-block !important;
            padding: 0.25rem 0 !important;
            opacity: 0.85 !important;
        }
        
        .ghibli-footer .footer-nav li a:hover,
        footer .footer-nav li a:hover {
            color: #DBC6A5 !important;
            opacity: 1 !important;
            transform: translateX(4px) !important;
        }
        
        .ghibli-footer .contact-text,
        footer .contact-text {
            color: #D0C8B0 !important;
            opacity: 0.85 !important;
        }
        
        .ghibli-footer .copyright,
        footer .copyright {
            color: #9A8E7A !important;
            font-size: 0.7rem !important;
        }
        
        .ghibli-footer .footer-motto,
        footer .footer-motto {
            color: #A89A82 !important;
            font-style: italic !important;
        }
        
        /* Footer divider - subtle warm */
        .ghibli-footer .footer-divider,
        footer .footer-divider {
            height: 1px !important;
            background: linear-gradient(90deg, transparent, rgba(160, 140, 110, 0.5), rgba(200, 180, 140, 0.3), rgba(160, 140, 110, 0.5), transparent) !important;
            margin: 1.5rem 0 1rem !important;
            border-radius: 2px !important;
        }
        
        /* Brand styles */
        .ghibli-footer .brand-totoro,
        footer .brand-totoro {
            width: 42px;
            height: auto;
            opacity: 0.7;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2)) brightness(0.95);
            transition: opacity 0.3s ease;
        }
        
        .ghibli-footer .footer-brand:hover .brand-totoro,
        footer .footer-brand:hover .brand-totoro {
            opacity: 0.9;
        }
        
        .ghibli-footer .footer-brand,
        footer .footer-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }
        
        .ghibli-footer .brand-description,
        footer .brand-description {
            font-size: 0.8rem;
            line-height: 1.6;
            margin-bottom: 0;
            max-width: 280px;
            color: #D8D0BC !important;
            opacity: 0.8 !important;
        }
        
        /* Contact items */
        .ghibli-footer .contact-item,
        footer .contact-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.6rem;
            transition: all 0.2s ease;
        }
        
        .ghibli-footer .contact-item:hover,
        footer .contact-item:hover {
            transform: translateX(4px);
        }
        
        .ghibli-footer .contact-icon,
        footer .contact-icon {
            min-width: 28px;
            font-size: 1rem;
            opacity: 0.7;
            filter: none;
        }
        
        .ghibli-footer .footer-nav,
        footer .footer-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .ghibli-footer .footer-nav li,
        footer .footer-nav li {
            margin-bottom: 0.5rem;
        }
        
        .ghibli-footer .footer-contact,
        footer .footer-contact {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        /* Footer bottom */
        .ghibli-footer .footer-bottom,
        footer .footer-bottom {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            font-size: 0.7rem;
            padding-top: 0.5rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .ghibli-footer, footer {
                padding: 2rem 0 1.5rem !important;
                margin-top: 3rem !important;
            }
            
            .ghibli-footer::before {
                display: none !important;
            }
            
            .ghibli-footer .footer-brand, footer .footer-brand {
                justify-content: center;
            }
            
            .ghibli-footer .brand-description, footer .brand-description {
                text-align: center;
                max-width: 100%;
            }
            
            .ghibli-footer .footer-title, footer .footer-title {
                text-align: center;
                margin-top: 1rem;
            }
            
            .ghibli-footer .footer-nav, footer .footer-nav {
                text-align: center;
            }
            
            .ghibli-footer .footer-nav li a:hover,
            footer .footer-nav li a:hover {
                transform: translateX(0) !important;
            }
            
            .ghibli-footer .footer-contact, footer .footer-contact {
                text-align: center;
            }
            
            .ghibli-footer .contact-item, footer .contact-item {
                justify-content: center;
            }
            
            .ghibli-footer .contact-item:hover,
            footer .contact-item:hover {
                transform: translateX(0) !important;
            }
            
            .ghibli-footer .footer-bottom, footer .footer-bottom {
                flex-direction: column;
                text-align: center;
            }
        }
        </style>
        
<?php else: ?>
        </main>
    </div>
    
    <!-- Admin Footer -->
    <footer class="admin-footer">
        <div class="container-fluid">
            <div class="admin-footer-content">
                <span>&copy; <?= date('Y') ?> Kelurahan Sasi - Admin Panel</span>
                <span class="admin-footer-separator">&bull;</span>
                <span>Sistem Informasi Kelurahan</span>
            </div>
        </div>
    </footer>
    
    <style>
    .admin-footer {
        background: linear-gradient(135deg, #2A3B2C 0%, #1E2A22 100%) !important;
        background-color: #243428 !important;
        background-image: none !important;
        padding: 0.75rem 0 !important;
        margin-top: auto !important;
        border-top: 1px solid rgba(160, 140, 110, 0.4) !important;
    }
    .admin-footer::before,
    .admin-footer::after {
        display: none !important;
    }
    .admin-footer-content {
        text-align: center;
        font-size: 0.75rem;
        color: rgba(240, 237, 229, 0.6);
    }
    .admin-footer-separator {
        margin: 0 0.5rem;
        color: rgba(199, 209, 181, 0.5);
    }
    </style>
    
<?php endif; ?>

<script src="<?= ASSETS_URL ?>/vendor/bootstrap/bootstrap.bundle.min.js"></script>
<script src="<?= ASSETS_URL ?>/js/main.js"></script>
<script src="<?= ASSETS_URL ?>/js/ghibli-effects.js"></script>

</body>
</html>