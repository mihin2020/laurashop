(function () {
    var authViewPrepared = false;
    function prepareAuthorizationView() {
        if (authViewPrepared) {
            return null;
        }

        var loginForm = document.getElementById('loginform');
        var lokaliseAuthorizationViewNodes = loginForm.getElementsByClassName('lokalise-authorization-form');
        if (lokaliseAuthorizationViewNodes.length === 0) {
            return null;
        }
        var lokaliseAuthorizationView = lokaliseAuthorizationViewNodes[0];

        loginForm.innerHTML = "";
        loginForm.classList.add('lokalise-authorization');
        loginForm.removeAttribute('action');
        loginForm.removeAttribute('method');
        authViewPrepared = true;

        loginForm.append(lokaliseAuthorizationView);

        return lokaliseAuthorizationView;
    }

    function bindUserActionEvents(view)
    {
        var acceptButton = view.getElementsByClassName('lokalise-user-action-accept');
        if (acceptButton.length > 0) {
            acceptButton[0].addEventListener('click', function () {
                window.location.href = window.location.href + '&choice=accept';
            });
        }

        var rejectButton = view.getElementsByClassName('lokalise-user-action-reject');
        if (rejectButton.length > 0) {
            rejectButton[0].addEventListener('click', function () {
                window.location.href = window.location.href + '&choice=reject';
            });
        }
    }

    document.addEventListener('readystatechange', function () {
        if (document.readyState !== 'interactive') {
            return;
        }

        var view = prepareAuthorizationView();
        if (view !== null) {
            bindUserActionEvents(view);
        }
    });
})();
