@push('styles')
<style>
	.app-sidebar3 {
		width: 270px;
		background: linear-gradient(185deg, #1b2141 0%, #0f1326 65%, #0a0d1a 100%);
		color: #e8ecff;
		box-shadow: 6px 0 24px rgba(10, 14, 40, 0.25);
		border-right: 1px solid rgba(255, 255, 255, 0.04);
	}

	.app-sidebar3 .side-menu {
		list-style: none;
		padding: 20px 18px 32px;
		margin: 0;
		overflow-y: auto;
		height: calc(100vh - 140px);
		scrollbar-width: thin;
		scrollbar-color: rgba(114, 135, 255, 0.35) transparent;
	}

	.app-sidebar3 .side-menu::-webkit-scrollbar {
		width: 6px;
	}

	.app-sidebar3 .side-menu::-webkit-scrollbar-track {
		background: transparent;
	}

	.app-sidebar3 .side-menu::-webkit-scrollbar-thumb {
		background: rgba(114, 135, 255, 0.35);
		border-radius: 999px;
	}

	.app-sidebar__user {
		padding: 32px 24px 18px;
		background: linear-gradient(180deg, rgba(255, 255, 255, 0.06) 0%, rgba(255, 255, 255, 0.02) 100%);
		border-bottom: 1px solid rgba(255, 255, 255, 0.05);
		text-align: center;
	}

	.app-sidebar__user .user-pic .avatar-xl {
		box-shadow: 0 12px 30px rgba(11, 15, 31, 0.35);
		border: 3px solid rgba(255, 255, 255, 0.35);
		width: 96px;
		height: 96px;
		object-fit: cover;
	}

	.app-sidebar__user .user-info h5 {
		color: #ffffff;
		font-size: 16px;
		margin-bottom: 4px;
	}

	.app-sidebar__user .user-info span {
		color: rgba(226, 232, 255, 0.7);
		font-size: 13px;
	}

	.side-menu__item {
		display: flex;
		align-items: center;
		gap: 12px;
		padding: 11px 20px 11px 16px;
		font-size: 14px;
		color: rgba(214, 219, 255, 0.92);
		border-radius: 14px;
		transition: all 0.25s ease;
		position: relative;
		text-decoration: none;
		font-weight: 500;
	}

	.side-menu__item .side-menu__label {
		flex: 1;
		line-height: 1.35;
		letter-spacing: 0.01em;
	}

	.side-menu__item .angle,
	.side-menu__item .fa-angle-right,
	.side-menu__item .fe-chevron-right {
		display: none !important;
	}

	.side-menu__item::after {
		content: "\f105";
		font-family: "Font Awesome 6 Free", "Font Awesome 5 Free", "FontAwesome";
		font-weight: 900;
		font-size: 12px;
		color: rgba(214, 219, 255, 0.55);
		margin-left: auto;
		transition: transform 0.25s ease, color 0.25s ease;
	}

	.side-menu__item:hover,
	.side-menu__item:focus {
		background: rgba(89, 106, 255, 0.22);
		color: #ffffff;
		box-shadow: inset 0 0 0 1px rgba(126, 142, 255, 0.38);
		text-decoration: none;
	}

	.side-menu__item:focus-visible {
		outline: 3px solid rgba(143, 161, 255, 0.55);
		outline-offset: 2px;
	}

	.side-menu__item:hover::after,
	.side-menu__item:focus::after {
		color: #ffffff;
		transform: translateX(2px);
	}

	.side-menu__icon,
	img.side-menu__icon {
		width: 22px;
		height: 22px;
		display: inline-flex;
		align-items: center;
		justify-content: center;
		color: inherit;
		opacity: 0.95;
	}

	.side-menu__icon svg,
	.side-menu__icon img,
	.side-menu__icon i,
	img.side-menu__icon {
		max-width: 100%;
		max-height: 100%;
		object-fit: contain;
	}

	.side-menu__icon i {
		font-size: 16px;
		line-height: 1;
	}

	.side-menu__item[data-state="open"]::after {
		transform: rotate(90deg);
		color: #ffffff;
	}

	.slide-menu {
		list-style: none;
		padding-left: 18px;
		margin: 8px 0 12px;
		border-left: 1px dashed rgba(154, 167, 255, 0.35);
	}

	.slide-menu .slide-item {
		display: flex;
		align-items: center;
		padding: 9px 18px 9px 14px;
		color: rgba(214, 219, 255, 0.85);
		border-radius: 12px;
		transition: all 0.2s ease;
		font-size: 13px;
		text-decoration: none;
	}

	.slide-menu .slide-item span {
		flex: 1;
	}

	.slide-menu .slide-item::after {
		content: "\f105";
		font-family: "Font Awesome 6 Free", "Font Awesome 5 Free", "FontAwesome";
		font-weight: 900;
		font-size: 11px;
		color: rgba(214, 219, 255, 0.45);
		margin-left: auto;
		transition: transform 0.25s ease, color 0.25s ease;
	}

	.slide-menu .slide-item:hover,
	.slide-menu .slide-item:focus {
		background: rgba(97, 115, 255, 0.18);
		color: #ffffff;
		text-decoration: none;
		box-shadow: inset 0 0 0 1px rgba(126, 142, 255, 0.25);
	}

	.slide-menu .slide-item:hover::after,
	.slide-menu .slide-item:focus::after {
		color: #ffffff;
		transform: translateX(2px);
	}

	.slide-menu .sub-slide {
		padding: 0;
	}

	.slide-menu .sub-slide .slide-item {
		padding-left: 22px;
	}

	@media (max-height: 768px) {
		.app-sidebar__user {
			padding: 24px 18px 14px;
		}

		.app-sidebar3 .side-menu {
			height: calc(100vh - 118px);
		}
	}

	@media (max-width: 991px) {
		.app-sidebar3 {
			width: 100%;
		}

		.app-sidebar__user {
			display: flex;
			align-items: center;
			gap: 16px;
			text-align: left;
		}

		.app-sidebar__user .user-pic .avatar-xl {
			width: 72px;
			height: 72px;
		}
	}
</style>
@endpush
