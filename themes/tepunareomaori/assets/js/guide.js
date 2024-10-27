const driver = window.driver.js.driver;

const driverObj = driver({
    showProgress: true // Shows a progress bar during the steps
});

/* for both School Admin and Teacher */
if ((document.body.className.match('role-school_leader') || document.body.className.match('role-school_principal')) && document.body.className.match('role-teacher')) {

    // In Classroom Page
    if (document.body.className.match('subgroups')) {
        const driverObj = driver({
            showProgress: true,
            steps: [
                {
                    element: ".menu-item.academy", // Highlight Academy menu item
                    popover: {
                        title: i18_string.press_here,
                        description: i18_string.manage_school_description
                    }
                },
                {
                    element: ".menu-item.onboarding", // Highlight Onboarding menu item
                    popover: {
                        title: i18_string.press_here,
                        description: i18_string.manage_teacher_description
                    }
                }
            ]
        });

        driverObj.drive();
    }

    // In course Academy Page
    if (document.body.className.match('academy_course')) {
        if (document.querySelector('.learndash_join_button.btn-advance-start')) {
            driverObj.highlight({
                element: ".learndash_join_button.btn-advance-start",
                popover: {
                    title: i18_string.start_course,
                }
            });
        }
        if (document.querySelector('.learndash_join_button.btn-advance-continue')) {
            driverObj.highlight({
                element: ".learndash_join_button.btn-advance-continue",
                popover: {
                    title: i18_string.continue_course,
                }
            });
        }
    }

    // In Onboarding course Page
    if (document.body.className.match('onboarding_course')) {
        if (document.querySelector('.learndash_join_button.btn-advance-start')) {
            driverObj.highlight({
                element: ".learndash_join_button.btn-advance-start",
                popover: {
                    title: i18_string.start_course,
                }
            });
        }
        if (document.querySelector('.learndash_join_button.btn-advance-continue')) {
            driverObj.highlight({
                element: ".learndash_join_button.btn-advance-continue",
                popover: {
                    title: i18_string.continue_course,
                }
            });
        }
    }
}

/* for School Admin only */
if ((document.body.className.match('role-school_leader') || document.body.className.match('role-school_principal')) && !document.body.className.match('role-teacher')) {
    // In Classroom Page
    if (document.body.className.match('subgroups')) {
        driverObj.highlight({
            element: ".menu-item.academy",
            popover: {
                title: i18_string.press_here,
                description: i18_string.manage_school_description
            }
        });
    }

    // In course Academy Page
    if (document.body.className.match('academy_course')) {
        if (document.querySelector('.learndash_join_button.btn-advance-start')) {
            driverObj.highlight({
                element: ".learndash_join_button.btn-advance-start",
                popover: {
                    title: i18_string.start_course,
                }
            });
        }
        if (document.querySelector('.learndash_join_button.btn-advance-continue')) {
            driverObj.highlight({
                element: ".learndash_join_button.btn-advance-continue",
                popover: {
                    title: i18_string.continue_course,
                }
            });
        }
    }
}

/* for Teacher only */
if (document.body.className.match('role-teacher') && !(document.body.className.match('role-school_leader') || document.body.className.match('role-school_principal'))) {
    // In Classroom Page
    if (document.body.className.match('subgroups')) {
        driverObj.highlight({
            element: ".menu-item.onboarding",
            popover: {
                title: i18_string.press_here,
                description: i18_string.manage_teacher_description
            }
        });
    }

    // In Onboarding course Page
    if (document.body.className.match('onboarding_course')) {
        if (document.querySelector('.learndash_join_button.btn-advance-start')) {
            driverObj.highlight({
                element: ".learndash_join_button.btn-advance-start",
                popover: {
                    title: i18_string.start_course,
                }
            });
        }
        if (document.querySelector('.learndash_join_button.btn-advance-continue')) {
            driverObj.highlight({
                element: ".learndash_join_button.btn-advance-continue",
                popover: {
                    title: i18_string.continue_course,
                }
            });
        }
    }
}
