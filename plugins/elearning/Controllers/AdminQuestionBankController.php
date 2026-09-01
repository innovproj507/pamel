<?php

namespace Plugins\Elearning\Controllers;

use Core\View;
use Plugins\Elearning\Models\QuestionBank;

/**
 * Banco de preguntas sin asignar.
 *
 * Permite revisar, reasignar a un quiz real o eliminar las preguntas que
 * quedaron apuntando a un quiz inexistente tras la importación.
 */
class AdminQuestionBankController extends BaseController
{
    private $bank;

    public function __construct()
    {
        parent::__construct();
        $this->bank = new QuestionBank();
    }

    /**
     * Listado de sets sin asignar.
     */
    public function index()
    {
        $this->requireCan('lms.quizzes.manage');

        $perPage = 10;
        $page    = max(1, (int) ($_GET['page'] ?? 1));
        $search  = trim($_GET['q'] ?? '');

        $total = $this->bank->countSets($search);
        $sets  = $this->bank->sets($perPage, ($page - 1) * $perPage, $search);

        // Muestra de preguntas para reconocer cada set de un vistazo
        foreach ($sets as &$set) {
            $set['muestra'] = $this->bank->questions((int) $set['set_id'], 3);
        }
        unset($set);

        $view = new View();
        $view->render('admin/views/lms/questions/bank', [
            'title'    => 'Preguntas sin asignar',
            'sets'     => $sets,
            'summary'  => $this->bank->summary(),
            'targets'  => $this->targetsForUser(),
            'total'    => $total,
            'page'     => $page,
            'perPage'  => $perPage,
            'lastPage' => max(1, (int) ceil($total / $perPage)),
            'q'        => $search,
        ], 'admin/views/layout');
    }

    /**
     * Detalle de un set: todas sus preguntas con las opciones.
     */
    public function show($setId)
    {
        $this->requireCan('lms.quizzes.manage');

        $setId     = (int) $setId;
        $questions = $this->bank->questions($setId);

        if (!$questions) {
            $this->flash('error', 'Ese set ya no tiene preguntas sin asignar.');
            $this->redirect('/manager/lms/questions/unassigned');
        }

        $view = new View();
        $view->render('admin/views/lms/questions/bank_set', [
            'title'     => 'Set ' . $setId,
            'setId'     => $setId,
            'questions' => $questions,
            'answers'   => $this->bank->answerCount($setId),
            'targets'   => $this->targetsForUser(),
        ], 'admin/views/layout');
    }

    /**
     * Reasigna el set a un quiz existente.
     */
    public function assign($setId)
    {
        $this->requireCan('lms.quizzes.manage');
        $this->validateCsrf('/manager/lms/questions/unassigned');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/manager/lms/questions/unassigned');
        }

        $setId  = (int) $setId;
        $target = (int) ($_POST['target_quiz_id'] ?? 0);

        if ($target <= 0) {
            $this->flash('error', 'Selecciona el quiz al que quieres mover las preguntas.');
            $this->redirect('/manager/lms/questions/unassigned');
        }

        // Un instructor solo puede mover hacia sus propios cursos.
        $quiz = $this->db->fetchOne("SELECT course_id FROM lms_quizzes WHERE id = ?", [$target]);
        if (!$quiz) {
            $this->flash('error', 'El quiz destino no existe.');
            $this->redirect('/manager/lms/questions/unassigned');
        }
        $this->authorizeCourseOwnership((int) $quiz['course_id']);

        $moved = $this->bank->assignSet($setId, $target);

        if ($moved === false) {
            $this->flash('error', 'No se pudieron mover las preguntas. Revisa el log de errores de PHP.');
        } else {
            $this->flash('success', sprintf(
                '%d pregunta(s) del set %d asignadas correctamente.',
                $moved,
                $setId
            ));
        }

        $this->redirect('/manager/lms/quizzes/' . $target . '/questions');
    }

    /**
     * Elimina el set completo.
     */
    public function delete($setId)
    {
        $this->requireCan('lms.quizzes.manage');
        $this->validateCsrf('/manager/lms/questions/unassigned');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/manager/lms/questions/unassigned');
        }

        $setId   = (int) $setId;
        $answers = $this->bank->answerCount($setId);
        $deleted = $this->bank->deleteSet($setId);

        if ($deleted === false) {
            $this->flash('error', 'No se pudo eliminar el set. Revisa el log de errores de PHP.');
        } else {
            $this->flash('success', $answers > 0
                ? sprintf('Set %d eliminado: %d pregunta(s) y %d respuesta(s) de alumnos.', $setId, $deleted, $answers)
                : sprintf('Set %d eliminado: %d pregunta(s).', $setId, $deleted));
        }

        $this->redirect('/manager/lms/questions/unassigned');
    }

    /**
     * Quizzes destino visibles para el usuario actual.
     */
    private function targetsForUser(): array
    {
        return $this->bank->targetQuizzes(
            $this->user['role'] === 'teacher' ? (int) $this->user['id'] : null
        );
    }
}
