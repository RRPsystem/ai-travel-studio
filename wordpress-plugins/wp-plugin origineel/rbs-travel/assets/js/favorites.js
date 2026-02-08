/**
 * RBS Travel Favorites System
 */
(function($) {
    'use strict';

    const RBSTravelFavorites = {
        storageKey: 'rbstravel_favorites',

        init: function() {
            this.loadFavorites();
            this.bindEvents();
        },

        bindEvents: function() {
            $(document).on('click', '.rbs-travel-favorite-btn', this.toggleFavorite.bind(this));
        },

        getFavorites: function() {
            const favorites = localStorage.getItem(this.storageKey);
            return favorites ? JSON.parse(favorites) : [];
        },

        saveFavorites: function(favorites) {
            localStorage.setItem(this.storageKey, JSON.stringify(favorites));
        },

        isFavorite: function(postId) {
            const favorites = this.getFavorites();
            return favorites.includes(postId);
        },

        addFavorite: function(postId) {
            let favorites = this.getFavorites();
            if (!favorites.includes(postId)) {
                favorites.push(postId);
                this.saveFavorites(favorites);
            }
        },

        removeFavorite: function(postId) {
            let favorites = this.getFavorites();
            favorites = favorites.filter(id => id !== postId);
            this.saveFavorites(favorites);
        },

        toggleFavorite: function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const $btn = $(e.currentTarget);
            const postId = parseInt($btn.data('post-id'));
            
            if (this.isFavorite(postId)) {
                this.removeFavorite(postId);
                $btn.removeClass('is-favorite');
                this.showNotification('Verwijderd uit favorieten');
            } else {
                this.addFavorite(postId);
                $btn.addClass('is-favorite');
                this.showNotification('Toegevoegd aan favorieten');
            }
            
            this.updateFavoritesCount();
            
            // If showing favorites only and item was removed, hide it
            if (this.showingFavoritesOnly && !this.isFavorite(postId)) {
                $btn.closest('.idealist_overview_item').fadeOut(300);
            }
        },

        loadFavorites: function() {
            const favorites = this.getFavorites();
            $('.rbs-travel-favorite-btn').each(function() {
                const postId = parseInt($(this).data('post-id'));
                if (favorites.includes(postId)) {
                    $(this).addClass('is-favorite');
                }
            });
        },

        showNotification: function(message) {
            // Remove existing notification
            $('.rbs-travel-notification').remove();
            
            // Create new notification
            const $notification = $('<div class="rbs-travel-notification">' + message + '</div>');
            $('body').append($notification);
            
            // Show notification
            setTimeout(function() {
                $notification.addClass('show');
            }, 10);
            
            // Hide and remove after 2 seconds
            setTimeout(function() {
                $notification.removeClass('show');
                setTimeout(function() {
                    $notification.remove();
                }, 300);
            }, 2000);
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        RBSTravelFavorites.init();
    });

})(jQuery);
