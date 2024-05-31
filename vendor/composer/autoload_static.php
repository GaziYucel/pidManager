<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit15d5b6ff68f0a1ed6524c4b013f4e435
{
    public static $classMap = array (
        'APP\\plugins\\generic\\pidManager\\classes\\Helpers\\PID\\AbstractPid' => __DIR__ . '/../..' . '/classes/Helpers/PID/AbstractPid.php',
        'APP\\plugins\\generic\\pidManager\\classes\\Helpers\\PID\\Doi' => __DIR__ . '/../..' . '/classes/Helpers/PID/Doi.php',
        'APP\\plugins\\generic\\pidManager\\classes\\Igsn\\IgsnArticleView' => __DIR__ . '/../..' . '/classes/Igsn/IgsnArticleView.php',
        'APP\\plugins\\generic\\pidManager\\classes\\Igsn\\IgsnConstants' => __DIR__ . '/../..' . '/classes/Igsn/IgsnConstants.php',
        'APP\\plugins\\generic\\pidManager\\classes\\Igsn\\IgsnDao' => __DIR__ . '/../..' . '/classes/Igsn/IgsnDao.php',
        'APP\\plugins\\generic\\pidManager\\classes\\Igsn\\IgsnDataModel' => __DIR__ . '/../..' . '/classes/Igsn/IgsnDataModel.php',
        'APP\\plugins\\generic\\pidManager\\classes\\Igsn\\IgsnForm' => __DIR__ . '/../..' . '/classes/Igsn/IgsnForm.php',
        'APP\\plugins\\generic\\pidManager\\classes\\Igsn\\IgsnPublicationTab' => __DIR__ . '/../..' . '/classes/Igsn/IgsnPublicationTab.php',
        'APP\\plugins\\generic\\pidManager\\classes\\Igsn\\IgsnSchema' => __DIR__ . '/../..' . '/classes/Igsn/IgsnSchema.php',
        'APP\\plugins\\generic\\pidManager\\classes\\Igsn\\IgsnSubmissionWizard' => __DIR__ . '/../..' . '/classes/Igsn/IgsnSubmissionWizard.php',
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInit15d5b6ff68f0a1ed6524c4b013f4e435::$classMap;

        }, null, ClassLoader::class);
    }
}
