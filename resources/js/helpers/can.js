import {Inertia} from '@inertiajs/inertia';

export default {
    install(app) {
        app.config.globalProperties.$can = function (permissions) {
            const userPermissions = localStorage.getItem('permissions') ? JSON.parse(localStorage.getItem('permissions')) : []
            let canEnter = false
            if (!Array.isArray(permissions)) {
                canEnter = userPermissions.includes(permissions);
            } else {
                permissions.forEach((permission) => {
                    if (userPermissions.includes(permission)) {
                        canEnter = true
                    }
                })
            }
            return canEnter
        };
    },
};
